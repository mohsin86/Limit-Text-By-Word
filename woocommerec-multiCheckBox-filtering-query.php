<?php
/*
 * Woocommerc Checkbox filtering
 */

function filter_shoop_posts_query($q)
{
    if (is_shop() || is_page('shop')) { // set conditions here
        // select only In Stock Product
        if (isset($_REQUEST['min_price']) || isset($_REQUEST['max_price'])) {
            $meta_query = [];

            $meta_query['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => '_regular_price',
                    'value' => 0,
                    'compare' => '!='
                ),
                array(
                    'key' => '_regular_price',
                    'value' => [intval($_REQUEST['min_price']), intval($_REQUEST['max_price'])],
                    'type' => 'numeric',
                    'compare' => 'BETWEEN'
                ));
            $q->set('meta_query', $meta_query);

        }

        /*
         * Use this for every taxonomy
         */
        if (isset($_REQUEST['taxonomy_name'])) {
            $default_page = false;
            $tax_query = (array)$q->get('tax_query');
            //   $tax_query['tax_query'] = array('relation' => 'AND');


            if (isset($_REQUEST['taxonomy_name'])) {
                $taxonomy_name = explode(",", $_REQUEST['taxonomy_name']);
                if (count($taxonomy_name) > 0) {
                    foreach ($taxonomy_name as $size) {
                        $term[] = array(
                            'taxonomy' => 'taxonomy_name',
                            'field' => 'slug',
                            'terms' => $size,
                        );
                    }
                    $tax_query[] = array_merge(array('relation' => 'OR'), $term);

                } else {
                    $tax_query[] = array(
                        'taxonomy' => 'taxonomy_name',
                        'field' => 'slug',
                        'terms' => $_REQUEST['taxonomy_name'],
                    );
                }

            }

            $q->set('tax_query', $tax_query);

        }

        // only grab sold product
        $q->set('meta_key', '_stock_status');
        $q->set('meta_value', 'instock');
        $q->set('orderby', array('date' => 'DESC'));

    }
}
