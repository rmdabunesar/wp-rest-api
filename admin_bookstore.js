/**
 * Using the WordPress REST API
 */
// Fatching Book
function fatchBook() {
    wp.apiFetch({
        path: "/wp/v2/books",
    }).then((books) => {
        const textarea = document.getElementById("bookstore-booklist");
        textarea.value = '';
        books.map((book) => {
        textarea.value +=
            "ID: " + book.id + "  Title: " + book.title.rendered + "\n";
        });
    });
}

const fetchBooksByRestButton = document.getElementById("bookstore-fetch-books");
if (fetchBooksByRestButton) {
    fetchBooksByRestButton.addEventListener("click", fatchBook);
}


// Creating Book
function createBook() {
    const title = document.getElementById("bookstore-book-title").value;
    const content = document.getElementById("bookstore-book-content").value;

    wp.apiFetch({
        path: "/wp/v2/books/",
        method: "POST",
        data: {
        title: title,
        content: content,
        status: "publish"
        },
    }).then((result) => {
        alert("Book saved!");
    });
}

const addBookButton = document.getElementById("bookstore-add-book");
if (addBookButton) {
    addBookButton.addEventListener("click", createBook);
}


// Updating Book
function updateBook() {
    const id = document.getElementById("bookstore-update-book-id").value;
    const newTitle = document.getElementById("bookstore-book-new-title").value;
    const newContent = document.getElementById("bookstore-book-new-content").value;

    wp.apiFetch({
        path: "/wp/v2/books/" + id,
        method: "POST",
        data: {
        title: newTitle,
        content: newContent,
        },
    }).then((result) => {
        alert("Book Updated!");
    });
}

const updateBookButton = document.getElementById("bookstore-update-book");
if (updateBookButton) {
    updateBookButton.addEventListener("click", updateBook);
}


// Deleting Book
function deleteBook() {
    const deleteId = document.getElementById("bookstore-delete-book-id").value;

    wp.apiFetch({
        path: "/wp/v2/books/" + deleteId,
        method: "DELETE",
    }).then((result) => {
        alert("Book deleted!");
    });
}

const deleteBookButton = document.getElementById("bookstore-delete-book");
if (deleteBookButton) {
    deleteBookButton.addEventListener("click", deleteBook);
}


/**
 * Adding Post to Other Website
 */
function createPost() {
    fetch("https://abunesar.ahnsolution.com/wp-json/wp/v2/posts", {
        method: "POST",
        headers: {
            "Authorization": "Basic " + btoa("rinkon:2MXweLh9NfK1XzANICH4pjEG"),
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            title: document.getElementById("post-title").value,
            content: document.getElementById("post-content").value,
            status: "publish"
        }),
    })
    .then(res => res.json())
    .then(() => alert("Post added!"))
    .catch(err => console.error("Error:", err));
}
const addPostButton = document.getElementById("add-post");
if (addPostButton) {
    addPostButton.addEventListener("click", createPost);
}