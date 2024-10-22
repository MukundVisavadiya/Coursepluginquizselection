<?php

add_action('init', 'as_ptclts_rewrite_rule');
function as_ptclts_rewrite_rule()
{
    add_rewrite_rule(
        '^course/([a-zA-Z0-9-]+)/chapters/([a-zA-Z0-9-]+)/?$',
        'index.php?course=$matches[1]&chapters=$matches[2]',
        'top'
    );

    add_rewrite_rule(
        '^course/([a-zA-Z0-9-]+)/chapters/([a-zA-Z0-9-]+)/lessons/([a-zA-Z0-9-]+)?$',
        'index.php?course=$matches[1]&chapters=$matches[2]&lessons=$matches[3]',
        'top'
    );

    add_rewrite_rule(
        '^course/([a-zA-Z0-9-]+)/chapters/([a-zA-Z0-9-]+)/lessons/([a-zA-Z0-9-]+)/topics/([a-zA-Z0-9-]+)?$',
        'index.php?course=$matches[1]&chapters=$matches[2]&lessons=$matches[3]&topics=$matches[4]',
        'top'
    );

    add_rewrite_rule(
        '^course/([a-zA-Z0-9-]+)/chapters/([a-zA-Z0-9-]+)/lessons/([a-zA-Z0-9-]+)/topics/([a-zA-Z0-9-]+)/sections/([a-zA-Z0-9-]+)?$',
        'index.php?course=$matches[1]&chapters=$matches[2]&lessons=$matches[3]&topics=$matches[4]&sections=$matches[5]',
        'top'
    );

    flush_rewrite_rules();
}

add_filter('query_vars', 'as_ptclts_query_vars');
function as_ptclts_query_vars($query_vars)
{
    $query_vars[] = 'chapters';
    $query_vars[] = 'lessons';
    $query_vars[] = 'topics';
    $query_vars[] = 'sections';
    return $query_vars;
}
