// ✅ Define fetchUsers in the global scope

function fetchUsers() {
  console.log("✅ fetchUsers() called");
  fetch(`${BASE_URL}assets/php/fetch_users.php`) // Use base URL
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      console.log("✅ Users fetched:", data);
      const userTable = document.getElementById("user-table");

      if (!userTable) {
        console.error("❌ User table not found.");
        return;
      }

      userTable.innerHTML = ""; // Clear the table body

      if (data.status === "success") {
        const users = data.data;
        if (users.length === 0) {
          userTable.innerHTML = "<tr><td colspan='5'>No users found</td></tr>";
          return;
        }

        users.forEach((user) => {
          const row = `
                      <tr>
                          <td>${escapeHtml(user.id)}</td>
                          <td>${escapeHtml(user.username)}</td>
                          <td>${escapeHtml(user.email)}</td>
                          <td>${escapeHtml(user.created_at)}</td>
                          <td>${escapeHtml(user.favorite_things || "N/A")}</td>
                      </tr>
                  `;
          userTable.innerHTML += row;
        });
      } else {
        alert(data.message || "Failed to fetch users");
      }
    })
    .catch((error) => {
      console.error("Error fetching users:", error);
      alert("An error occurred while fetching users. Please try again later.");
    });
}
// ✅ Escape HTML to prevent XSS attacks
function escapeHtml(str) {
  if (typeof str !== "string") return str; // Handle non-string values gracefully
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// ✅ Initialize event listeners when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  console.log("✅ DOM fully loaded.");
  fetchUsers(); // Call fetchUsers on initial load
});
