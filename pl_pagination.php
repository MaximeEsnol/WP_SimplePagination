<?php
/**
* Plugin Name: Simple Pagination
* Plugin URI: https://www.perrylegion.com/
* Description: Pagination to navigate between next and previous pages.
* Version: 1.0
* Author: Maxime Esnol
* Author URI: http://maximeesnol.com/
**/


/**
*   Generates an HTML string to display a pagination bar, based on the provided maximum amount of pages.
*
*   @param int $maxNumPages The maximum amount of pages. If you use a paged WP_Query, this number can 
*                        number can be obtained by doing WP_Query::max_num_pages.
*
*   @return String A string containing HTML to display a pagination bar.
*/
function get_the_pagination($maxNumPages){
    $current_url = get_full_permalink();
    $params = get_request_params();

    $next_page = calculate_next_page(1, $maxNumPages);
    $prev_page = calculate_prev_page(1);
    $current_page = 1;

    if(isset($params["page"]) && intval($params["page"])){
        $next_page = calculate_next_page($params["page"], $maxNumPages);
        $prev_page = calculate_prev_page($params["page"], $maxNumPages);
        $current_page = $params["page"];
    }

    return html_pagination($next_page, $prev_page, $maxNumPages, $current_page);
}

/**
 * Echoes the string returned by get_the_pagination() function.
 * 
 * @param int $maxNumPages See get_the_pagination($maxNumPages).
 */
function the_pagination($maxNumPages){
    echo get_the_pagination($maxNumPages);
}

/**
 * Calculates what the next page is. If the next page is over the $max_pages limit, then null is returned, 
 * indicating that there is no next page.
 * 
 * @param int $current_page The current active page, or the page that comes before the potential next page.
 * @param int $max_pages The maximum amount of pages available.
 * 
 * @return mixed null if there is no next page, the page number if there is a next page.
 */
function calculate_next_page($current_page, $max_pages){
    return ($current_page == $max_pages) ? null : $current_page + 1;
}

/**
 * Calculates what the previous page is. If the previous page is the first page, then null is returned, 
 * indicating that there is no previous page.
 * 
 * @param int $current_page The current active page, or the page that comes after the potential previous page.
 * 
 * @return mixed null if there is no previous page or the page number if there is a previous page.
 */
function calculate_prev_page($current_page){
    return ($current_page <= 1) ? null : $current_page - 1;
}

/**
 * Gets the link of the current viewed page by the user, with the folder and file name if this applies.
 * Examples of what this function can return are: 
 * http://yoursite.com, https://yoursite.com/pages, https://yoursite.com/index.php,...
 * This function does <b>not</b> include potential query parameters. If a '?' is present in the current URL, 
 * that and everything that comes after it is discarded.
 * 
 * @return string The URL of the currently viewed page, without any query parameters.
 */
function get_domain(){
    return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . explode("?", $_SERVER['REQUEST_URI'])[0];
}

/**
 * Gets the full URL of the current viewed page. This returns the value of 
 * get_domain() and appends the query parameters if there are any. If there are no 
 * query parameters, the same value as get_domain() is returned.
 * 
 * @return string The URL of the currently viewed page, with any potential query parameters.
 */
function get_full_permalink(){
    $request_params = explode("?", $_SERVER["REQUEST_URI"]);

    if(count($request_params) < 2){
        return get_domain();
    } else {
        return get_domain() . "?" . $request_params[1];
    }
}

/**
 * Echoes the result of get_full_permalink().
 */
function the_full_permalink(){
    echo get_full_permalink();
}

/**
 * Gets the request parameters or query parameters, of the currently viewed web page. 
 * This only gets GET variables and does so by extracting them from the URL. 
 * 
 * @return mixed An associative array where the key is the name of the parameter and the value is its value.
 *  This function will return null if there are no query parameters.
 */
function get_request_params(){
    $url = get_full_permalink();
    $uri = explode("?", $url);

    if(count($uri) == 2){
        $params = explode("&", $uri[1]);
        $requestArray = array();
    
        foreach($params as $param){
            $arr = explode("=", $param);
            $requestArray[$arr[0]] = $arr[1];
        }
        
        return $requestArray;
    } else {
        return null;
    }
}

/**
 * Creates a string containing all the provided query parameters in the $params array.
 * The string looks like: "?key=value&key1=value1...&keyn=valuen".
 * 
 * @param $params An associative array of the query parameters to add to the string. 
 * The key in this array is the name of the parameter and the value is the value.
 * 
 * @return string A string with the query parameters. If there are none, an empty string is returned.
 */
function create_uri_string($params){
    $simpleParams = array();

    if($params != null){
        foreach($params as $key => $value){
            array_push($simpleParams, $key . "=" . $value);
        }
    
        $paramsString = implode("&", $simpleParams);
        return "?" . $paramsString;
    } else {
        return "";
    }
}

/**
 * Creates the HTML to display the pagination. The pagination is wrapped in a <div> element as an <ul>. 
 * 
 * @param int $next_page The next page from the perspective of the currently viewed page.
 * @param int $prev_page The previous page from the perspective of the currently viewed page.
 * @param int $total_pages The total amount of pages.
 * @param int $current_page The current active page number.
 * 
 * @return string an HTML string.
 */
function html_pagination($next_page, $prev_page, $total_pages, $current_page){
    $pageParams = get_request_params();
    
    $html = "<div class='pl-pagination'><ul>";
        if($prev_page != null){
            $pageParams["page"] = $prev_page;
            $html .= "<li class='page-button previous'><a href='". get_domain() . create_uri_string($pageParams) ."'>".svg_previous()."</a></li>";
        }
        
        if(($secondPreviousPage = calculate_prev_page($prev_page)) != null){
            $pageParams["page"] = $secondPreviousPage;
            $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>".$secondPreviousPage."</a></li>";

            if($secondPreviousPage != 1){
                $pageParams["page"] = 1;
                $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>1...</a></li>";
            }
        }

        if($prev_page != null){
            $pageParams["page"] = $prev_page;
            $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>".$prev_page."</a></li>";
        }

        $html .= "<li class='current'>".$current_page."</li>";

        if($next_page != null){
            $pageParams["page"] = $next_page;
            $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>".$next_page."</a></li>";

            if(($secondNextPage = calculate_next_page($next_page, $total_pages)) != null){
                $pageParams["page"] = $secondNextPage;
                $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>".$secondNextPage."</a></li>";

                if($secondNextPage != $total_pages){
                    $pageParams["page"] = $total_pages;
                    $html .= "<li class='page-number'><a href='". get_domain() . create_uri_string($pageParams) ."'>...".$total_pages."</a></li>";
                }
            }
        }

        if($next_page != null){
            $pageParams["page"] = $next_page;
            $html .= "<li class='page-button next'><a href='". get_domain() . create_uri_string($pageParams) ."'>".svg_next()."</a></li>";
        }
        
    $html .= "</ul></div>";
    return $html;
}

/**
 * Creates an SVG element for a chevron icon facing left.
 * 
 * @return string an HTML string containing only an SVG element.
 */
function svg_previous(){
    $svg = '<svg style="width:24px;height:24px" viewBox="0 0 24 24">';
        $svg .= '<path fill="currentColor" d="M15.41,16.58L10.83,12L15.41,7.41L14,6L8,12L14,18L15.41,16.58Z" />';
    $svg .= '</svg>';

    return $svg;
}

/**
 * Creates an SVG element for a chevron icon facing right.
 * 
 * @return string an HTML string containing only an SVG element.
 */
function svg_next(){
    $svg = '<svg style="width:24px;height:24px" viewBox="0 0 24 24">';
        $svg .= '<path fill="currentColor" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" />';
    $svg .= '</svg>';

    return $svg;
}