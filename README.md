# WP_SimplePagination
A simple pagination plugin for WordPress.

## How to install?
1. Download WP_SimplePagination
2. Extract the zip and copy the WP_SimplePagination folder into your "wp-content/plugins" folder on your WordPress site.
3. Go to the WordPress admin site, and enable Simple Pagination.

## How to use?
1. Simple! Wherever in your theme you want to add a pagination, just call the `the_pagination($max_pages)` function. 
`$max_pages` represents the maximum amount of pages you can show. If you used a paged WP_Query, you can find this number
by getting the max_num_pages property of WP_Query. Like this: `$wp_query->max_num_pages;`

2. That's all! Additionally (and you probably should!), you can style the pagination with CSS. The pagination is 
represented by a `<ul>` element wrapped 
inside a `<div class="pl-pagination">`.

## Enjoy :)
