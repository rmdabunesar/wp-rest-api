const loadBooksByRestButton = document.getElementById( 'bookstore-load-books' );
if ( loadBooksByRestButton ) {
    loadBooksByRestButton.addEventListener( 'click', function () {
        wp.apiRequest({ path: 'wp/v2/book?per_page=100' }).then(
            function ( books ) {
                const textarea = document.getElementById( 'bookstore-booklist' );
                textarea.value = '';
                books.forEach( function ( book ) {
                    textarea.value += book.title.rendered + ',' + book.link + ',\n';
                });
            }
        );
    });
}