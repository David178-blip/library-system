import express from "express";
import bodyParser from "body-parser";
import cors from "cors";
import mysql from "mysql2/promise";

const app = express();
app.use(cors());
app.use(bodyParser.json());

// ✅ Database connection
const db = await mysql.createConnection({
  host: "127.0.0.1",
  user: "root",
  password: "",
  database: "library_system",
});

// ✅ Rule-based Chatbot Function
function genericReply() {
  const replies = [
    "That's interesting! Tell me more.",
    "Hmm, I see what you mean.",
    "Can you explain that a bit further?",
    "I’m here to help! 😊",
  ];
  return replies[Math.floor(Math.random() * replies.length)];
}

async function libraryBotReply(message) {
  const lower = message.toLowerCase().trim();
  const words = lower.split(/\s+/);

  // ✅ Help commands
  if (words.includes("help")) {
    return (
      "Here’s what I can do:\n" +
      "- list books / show books 📚\n" +
      "- search <title> 🔍\n" +
      "- copies of <title> 📦"
    );
  }

  // ✅ Show all books
  if (lower.includes("list books") || lower.includes("show books")) {
    const [rows] = await db.query("SELECT title, author, year, copies FROM books");

    if (!rows.length) {
      return "No books available in the database.";
    }

    return (
      "Here are the available books:\n" +
      rows
        .map(
          (b, i) =>
            `${i + 1}. ${b.title} by ${b.author} (${b.year}) - Copies: ${b.copies}`
        )
        .join("\n")
    );
  }

  // ✅ Search by title (search, find, look for)
  if (lower.startsWith("search") || lower.startsWith("find") || lower.startsWith("look for")) {
    const title = lower.replace(/^(search|find|look for)/, "").trim();
    if (!title) return "Please specify the title you want to search. 😊";

    const [rows] = await db.query(
      "SELECT title, author, year, copies FROM books WHERE title LIKE ?",
      [`%${title}%`]
    );

    if (!rows.length) {
      return `No results found for "${title}".`;
    }

    return (
      "Here are the matching books:\n" +
      rows
        .map(
          (b) =>
            `- ${b.title} by ${b.author} (${b.year}) - Copies: ${b.copies}`
        )
        .join("\n")
    );
  }

  // ✅ Ask how many copies of a book
  if (lower.startsWith("copies of")) {
    const title = lower.replace("copies of", "").trim();

    if (!title) return "Please specify the book title. 😊";

    const [rows] = await db.query(
      "SELECT title, copies FROM books WHERE title LIKE ?",
      [`%${title}%`]
    );

    if (!rows.length) {
      return `I couldn’t find any book titled "${title}".`;
    }

    const book = rows[0];
    return `There are ${book.copies} copies of "${book.title}".`;
  }

  // ✅ Fallback
  return null;
}

// ✅ API Endpoint
app.post("/chat", async (req, res) => {
  const { message } = req.body;

  try {
    const reply = await libraryBotReply(message);
    res.json({ reply: reply || genericReply() });
  } catch (err) {
    console.error(err);
    res.json({ reply: "⚠️ Something went wrong while accessing the database." });
  }
});

// ✅ Start Server
app.listen(3000, () => {
  console.log("✅ Chatbot server is running at http://localhost:3000");
});
