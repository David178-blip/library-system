import express from "express";
import bodyParser from "body-parser";
import cors from "cors";
import dotenv from "dotenv";
import mysql from "mysql2/promise";

dotenv.config();

const app = express();
app.use(cors());
app.use(bodyParser.json());

const BOT_NAME = "Libby";
const REFUSAL = `${BOT_NAME}: I'm sorry, but I can only answer questions related to our library system — books, availability, authors, borrowing procedures, return deadlines, and fines.`;

// Simple memory for last search results
const userMemory = { lastResults: [] };

// MySQL connection
const db = await mysql.createConnection({
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
});

// ==========================
// NORMALIZATION
// ==========================
function extractSearchTerm(message) {
    let text = message.toLowerCase().trim();

    const aboutMatch = text.match(/books?\s+(?:about|on)\s+(.+)/);
    if (aboutMatch) {
        text = aboutMatch[1].trim();
    } else {
        const idx = text.lastIndexOf("about ");
        if (idx !== -1) {
            let tail = text.slice(idx + 6).trim();
            tail = tail.replace(/\bthen\b.*$/, "").trim();
            if (tail.length >= 3) text = tail;
        }
    }
    return text;
}

// ==========================
// GREETINGS
// ==========================
function isGreeting(message) {
    const text = message.toLowerCase().trim();
    const singleWord = ["hi", "hello", "hey", "yo"];
    const tokens = text.split(/[^a-z]+/).filter(Boolean);
    if (tokens.some(t => singleWord.includes(t))) return true;

    const multiWord = [
        "good morning", "good afternoon",
        "good evening", "how are you", "who are you"
    ];
    return multiWord.some(g => text === g || text.startsWith(g));
}

// ==========================
// STRICT: BOOK-RELATED DETECTION
// ==========================
function isBookRelated(text) {
    text = text.toLowerCase();
    const keywords = [
        "book", "books", "author", "title",
        "copy", "copies", "available", "availability",
        "library", "borrow", "return", "due date",
        "fine", "fines", "overdue", "penalty",
        "catalog", "search", "reading"
    ];
    return keywords.some(k => text.includes(k));
}

// ==========================
// STRICT: Block general knowledge BUT ALLOW “how to borrow”
// ==========================
const forbiddenPatterns = [
    /who is/i, /what is/i, /why/i,
    /explain/i, /define/i,
    /history/i, /calculate/i,
    /solve/i, /meaning of/i
];

// ==========================
// SIMPLE SEARCH CHECK
// ==========================
function userIsSearching(message) {
    const text = message.toLowerCase().trim();
    if (text.length < 2) return false;
    if (/^\d+$/.test(text)) return false;
    return true;
}

// ==========================
// COURSE DETECTION
// ==========================
const COURSE_KEYWORDS = {
    bsit: "BSIT",
    bscrim: "BSCRIM",
    bsba: "BSBA",
    bsed: "BSED",
    beed: "BEED",
    "bachelor of science in information technology": "BSIT",
    "bachelor of science in criminology": "BSCRIM",
    "bachelor of science in business administration": "BSBA",
    "bachelor of secondary education": "BSED",
    "bachelor of elementary education": "BEED",
};

function detectCourseCode(message) {
    const text = message.toLowerCase();
    for (const [keyword, code] of Object.entries(COURSE_KEYWORDS)) {
        if (text.includes(keyword)) return code;
    }
    return null;
}

// ==========================
// GENERIC BOOK REQUESTS
// ==========================
function isGenericBookRequest(text) {
    text = text.toLowerCase().trim();
    const exact = [
        "book", "books", "give me books",
        "show books", "recommend books",
        "any books", "list books"
    ];
    return exact.includes(text);
}

// ==========================
// LIST BOOKS
// ==========================
async function listSomeBooks(limit = 10) {
    const [rows] = await db.query(
        "SELECT title, author, copies FROM books ORDER BY id DESC LIMIT ?",
        [limit]
    );

    if (!rows.length) return null;

    userMemory.lastResults = rows.map(
        (b) => `${b.title} by ${b.author} — ${b.copies} copies`
    );

    return userMemory.lastResults
        .map((b, i) => `${i + 1}. ${b}`)
        .join("\n");
}

// ==========================
// LIST BOOKS BY COURSE
// ==========================
async function listBooksByCourse(courseCode, limit = 10) {
    const [rows] = await db.query(
        "SELECT title, author, copies FROM books WHERE course = ? ORDER BY id DESC LIMIT ?",
        [courseCode, limit]
    );

    if (!rows.length) return null;

    userMemory.lastResults = rows.map(
        (b) => `${b.title} by ${b.author} — ${b.copies} copies`
    );

    return userMemory.lastResults
        .map((b, i) => `${i + 1}. ${b}`)
        .join("\n");
}

// ==========================
// SEARCH DB
// ==========================
async function searchDatabaseBooks(message) {
    const term = extractSearchTerm(message);
    let keywords = term.split(/\s+/).filter(w => w.length >= 3);
    if (!keywords.length) keywords = [term];

    let sql;
    let params = [];

    if (keywords.length === 1) {
        sql = `
            SELECT title, author, copies
            FROM books
            WHERE LOWER(title) LIKE ? OR LOWER(author) LIKE ?
            LIMIT 10
        `;
        params = [`%${keywords[0]}%`, `%${keywords[0]}%`];
    } else {
        const conditions = keywords
            .map(() => "(LOWER(title) LIKE ? OR LOWER(author) LIKE ?)")
            .join(" OR ");

        sql = `
            SELECT title, author, copies
            FROM books
            WHERE ${conditions}
            LIMIT 10
        `;

        keywords.forEach(k => {
            params.push(`%${k}%`, `%${k}%`);
        });
    }

    const [rows] = await db.query(sql, params);
    if (!rows.length) return null;

    userMemory.lastResults = rows.map(
        (b) => `${b.title} by ${b.author} — ${b.copies} copies`
    );

    return userMemory.lastResults
        .map((b, i) => `${i + 1}. ${b}`)
        .join("\n");
}

// ==========================
// CHAT ENDPOINT
// ==========================
app.post("/chat", async (req, res) => {
    const { message } = req.body;

    try {
        if (!message || !message.trim()) {
            return res.json({
                reply: `${BOT_NAME}: I can help you find books in our catalog, check availability, and explain borrowing rules.`
            });
        }

        const trimmed = message.trim();
        const lower = trimmed.toLowerCase();

        // 1️⃣ Number selection
        if (/^\d+$/.test(trimmed)) {
            const num = parseInt(trimmed, 10);

            if (!userMemory.lastResults.length)
                return res.json({ reply: `${BOT_NAME}: Please search for books first.` });

            if (num < 1 || num > userMemory.lastResults.length)
                return res.json({
                    reply: `${BOT_NAME}: Please choose a number between 1 and ${userMemory.lastResults.length}.`
                });

            const selected = userMemory.lastResults[num - 1];
            return res.json({ reply: `${BOT_NAME}: You selected:<br>${selected}` });
        }

        // 2️⃣ Greetings
        if (isGreeting(message)) {
            return res.json({
                reply: `${BOT_NAME}: Hello! I can help you with books, authors, availability, borrowing rules, and fines.`
            });
        }

        // 3️⃣ Block NON-library questions
        if (!isBookRelated(message) || forbiddenPatterns.some(p => p.test(message))) {
            return res.json({ reply: REFUSAL });
        }

        // 4️⃣ Library borrowing / fine / rules questions
        const libraryKeywords = [
            "borrow", "borrowing", "how to borrow",
            "return", "due date", "overdue",
            "fine", "fines", "penalty"
        ];

        const isLibraryQuestion = libraryKeywords.some(k =>
            lower.includes(k)
        );

        if (isLibraryQuestion) {
            const reply =
                `${BOT_NAME}:<br>` +
                `• To borrow a book, search the book/books you want to borrow in the "Books" section, select borrow and wait for admin approval.<br>` +
                `• To get the physical book, present your valid school ID.<br>` +
                `• Borrowing period for books: 'Text book 3 days', 'Fiction book 7 days.<br>` +
                `• All items must be checked out at the circulation desk.<br>` +
                `• Books must be returned on or before the due date.<br>` +
                `• Overdue fines are 2 PHP per day per item.<br>` +
                `• Lost book must be replaced with the same title, author, and copyright date.<br>` +
                `• Damaged book must be paid by its original price.<br>` +
                `• You cannot borrow new materials if you have unpaid fines or overdue items.`;
            return res.json({ reply });
        }

        // 5️⃣ Course-specific recommendations (e.g. "books for BSIT")
        const courseCode = detectCourseCode(message);
        if (courseCode) {
            const list = await listBooksByCourse(courseCode, 10);
            if (list) {
                return res.json({
                    reply: `${BOT_NAME}: Here are some recommended books for ${courseCode}:<br>${list.replace(/\n/g, "<br>")}`
                });
            }
        }

        // 6️⃣ Generic book list
        if (isGenericBookRequest(message)) {
            const list = await listSomeBooks(10);
            if (list) {
                return res.json({
                    reply: `${BOT_NAME}: Here are some books from our library:<br>${list.replace(/\n/g, "<br>")}`
                });
            }
            return res.json({ reply: `${BOT_NAME}: There are no books stored in the system yet.` });
        }

        // 7️⃣ Database search
        let dbMatches = null;
        if (userIsSearching(message)) dbMatches = await searchDatabaseBooks(message);

        if (dbMatches) {
            return res.json({
                reply: `${BOT_NAME}: Here are books related to your search:<br>${dbMatches.replace(/\n/g, "<br>")}`
            });
        }

        // 7️⃣ No matches — fallback
        const fallback = await listSomeBooks(5);
        if (fallback) {
            return res.json({
                reply: `${BOT_NAME}: I couldn't find exact matches. Here are some other books:<br>${fallback.replace(/\n/g, "<br>")}`
            });
        }

        return res.json({ reply: `${BOT_NAME}: No books found in the system.` });

    } catch (err) {
        console.error("SERVER ERROR:", err);
        res.json({ reply: `${BOT_NAME}: ⚠️ Something went wrong.` });
    }
});

// ==========================
// START SERVER
// ==========================
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`✅ ${BOT_NAME} running at http://localhost:${PORT}`);
});
