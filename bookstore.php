<?php
/**
 * Plugin Name: Bookstore
 * Description: A plugin to manage books
 * Version: 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Using the WordPress REST API
 */
add_action( 'init', 'bookstore_register_book_post_type' );
function bookstore_register_book_post_type() {
    $args = [
        'labels'       => [
            'name'          => 'Books',
            'singular_name' => 'Book',
            'menu_name'     => 'Books',
            'add_new'       => 'Add New Book',
            'add_new_item'  => 'Add New Book',
            'new_item'      => 'New Book',
            'edit_item'     => 'Edit Book',
            'view_item'     => 'View Book',
            'all_items'     => 'All Books',
        ],
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'rest_base'    => 'books',
        'supports'     => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'],
    ];

    register_post_type('book', $args);

    register_meta(
        'post',
        'isbn',
        array(
            'single'         => true,
            'type'           => 'string',
            'default'        => '',
            'show_in_rest'   => true,
            'object_subtype' => 'book',
        )
    );
}

add_action( 'admin_enqueue_scripts', 'bookstore_admin_enqueue_scripts' );
function bookstore_admin_enqueue_scripts() {
    wp_enqueue_script(
        'bookstyle-script',
        plugins_url() . '/bookstore/admin_bookstore.js',
        ['wp-api-fetch'],
        '1.0.0',
        true
    );
}

add_action( 'admin_menu', 'bookstore_add_booklist_submenu', 11 );
function bookstore_add_booklist_submenu() {
    add_submenu_page(
        'edit.php?post_type=book',
        'Book List',
        'Book List',
        'edit_posts',
        'book-list',
        'bookstore_render_booklist'
    );
}

function bookstore_render_booklist() {
    ?>
    <div class="fatch-book">
        <h1>Actions</h1>
		<button id="bookstore-fetch-books">Fetch Books</button>
        <h2>Fatch Books</h2>
        <textarea id="bookstore-booklist" cols="100" rows="15"></textarea>
    </div>

    <div class="add-book">
        <h2>Add Book</h2>
        <form>
            <div>
                <label for="bookstore-book-title">Book Title</label>
                <input type="text" id="bookstore-book-title" placeholder="Title">
            </div>
            <div>
                <label for="bookstore-book-content">Book Content</label>
                <textarea id="bookstore-book-content" cols="100" rows="10"></textarea>
            </div>
            <div>
                <input type="button" id="bookstore-add-book" value="Add">
            </div>
        </form>
    </div>

    <div class="update-book">
        <h2>Update Book</h2>
        <form>
            <div>
                <label for="bookstore-update-book-id">Book ID</label>
                <input type="number" id="bookstore-update-book-id" placeholder="ID">
            </div>
            <div>
                <label for="bookstore-book-new-title">Book Title</label>
                <input type="text" id="bookstore-book-new-title" placeholder="Title">
            </div>
            <div>
                <label for="bookstore-book-new-content">Book Content</label>
                <textarea id="bookstore-book-new-content" cols="100" rows="10"></textarea>
            </div>
            <div>
                <input type="button" id="bookstore-update-book" value="Update">
            </div>
        </form>
    </div>

    <div class="update-book">
        <h2>Delete Book</h2>
        <form>
            <div>
                <label for="bookstore-delete-book-id">Book ID</label>
                <input type="number" id="bookstore-delete-book-id" placeholder="ID">
            </div>
            <div>
                <input type="button" id="bookstore-delete-book" value="Delete">
            </div>
        </form>
    </div>
    <?php
}


/**
 * Extending the WordPress REST API
 */
add_action( 'rest_api_init', 'bookstore_add_rest_fields' );
function bookstore_add_rest_fields() { 
    register_rest_field(
        'book',
        'isbn',
        array(
            'get_callback'    => 'bookstore_rest_get_isbn',
            'update_callback' => 'bookstore_rest_update_isbn',
            'schema'          => array(
                'description' => __( 'The ISBN of the book' ),
                'type'        => 'string',
            ),
        )
    );
}

function bookstore_rest_get_isbn( $book ){
    return  get_post_meta( $book['id'], 'isbn', true );
}

function bookstore_rest_update_isbn( $value, $book ){
    return update_post_meta( $book->ID, 'isbn', $value );
}