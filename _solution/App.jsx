// ✅ KUNCI JAWABAN Sesi 7 — threadly-react/src/App.jsx
// Login TETAP lokal (array users). Yang pindah ke API: feed, create post, like/dislike.
// Base URL dari import.meta.env.VITE_API_URL (set di threadly-react/.env).

import { useState, useEffect } from "react";
import Navbar from "./components/Navbar";
import LoginPage from "./pages/LoginPage";
import HomePage from "./pages/HomePage";
import ProfilePage from "./pages/ProfilePage";
import { users } from "./data/user";

const API_URL = import.meta.env.VITE_API_URL;

function App() {
  const [page, setPage] = useState("login"); // "login" | "home" | "profile"
  const [currentUser, setCurrentUser] = useState(null);
  const [posts, setPosts] = useState([]);
  const [reactions, setReactions] = useState({});
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Ambil feed dari API saat pertama kali render
  useEffect(() => {
    fetch(`${API_URL}/api/posts`)
      .then((res) => {
        if (!res.ok) throw new Error("Gagal memuat post");
        return res.json();
      })
      .then((data) => {
        // normalisasi: user_id (Laravel) → userId (React)
        const normalized = data.posts.map((post) => ({
          ...post,
          userId: post.user_id,
        }));
        setPosts(normalized);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  function handleLogin(username, password) {
    const found = users.find(
      (u) => u.username === username && u.password === password
    );
    if (!found) return "Username atau password salah";
    setCurrentUser(found);
    setPage("home");
    return null;
  }

  function handleLogout() {
    setCurrentUser(null);
    setPage("login");
  }

  async function handleLike(postId) {
    const current = reactions[postId];

    try {
      const res = await fetch(`${API_URL}/api/posts/${postId}/like`, {
        method: "POST",
        headers: { Accept: "application/json" },
      });
      if (!res.ok) throw new Error("Gagal like");
      const data = await res.json();
      const updated = { ...data.post, userId: data.post.user_id };
      setPosts((posts) => posts.map((p) => (p.id === postId ? updated : p)));
      setReactions((prev) => ({
        ...prev,
        [postId]: current === "like" ? null : "like",
      }));
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleDislike(postId) {
    const current = reactions[postId];

    try {
      const res = await fetch(`${API_URL}/api/posts/${postId}/dislike`, {
        method: "POST",
        headers: { Accept: "application/json" },
      });
      if (!res.ok) throw new Error("Gagal dislike");
      const data = await res.json();
      const updated = { ...data.post, userId: data.post.user_id };
      setPosts((posts) => posts.map((p) => (p.id === postId ? updated : p)));
      setReactions((prev) => ({
        ...prev,
        [postId]: current === "dislike" ? null : "dislike",
      }));
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleNewPost(content) {
    try {
      const res = await fetch(`${API_URL}/api/posts`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          user_id: currentUser.id, // kirim snake_case sesuai validasi backend
          content: content,
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        // status 422 → validasi gagal
        throw new Error(data.message ?? "Gagal membuat post");
      }

      // tampilkan post dari server (sudah punya id asli), terjemahkan userId
      const newPost = { ...data.post, userId: data.post.user_id };
      setPosts((prev) => [newPost, ...prev]);
    } catch (err) {
      setError(err.message);
    }
  }

  if (page === "login") {
    return <LoginPage onLogin={handleLogin} />;
  }

  return (
    <>
      <Navbar page={page} onNavigate={setPage} onLogout={handleLogout} />

      {loading && <p className="container">Loading...</p>}
      {error && <p className="container">⚠️ {error}</p>}

      {!loading && !error && page === "home" && (
        <HomePage
          posts={posts}
          users={users}
          currentUser={currentUser}
          reactions={reactions}
          onLike={handleLike}
          onDislike={handleDislike}
          onNewPost={handleNewPost}
        />
      )}

      {!loading && !error && page === "profile" && (
        <ProfilePage
          posts={posts}
          currentUser={currentUser}
          reactions={reactions}
          onLike={handleLike}
          onDislike={handleDislike}
        />
      )}
    </>
  );
}

export default App;
