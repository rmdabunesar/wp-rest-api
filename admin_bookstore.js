const fetchBooksByRestButton = document.getElementById("bookstore-fetch-books");
if (fetchBooksByRestButton) {
    fetchBooksByRestButton.addEventListener("click", function () {
        wp.apiFetch({ path: "/wp/v2/book" }).then((books) => {
        const textarea = document.getElementById("bookstore-booklist");
        books.map((book) => {
            textarea.value += book.title.rendered + "," + book.link + ",\n";
        });
        });
    });
}
