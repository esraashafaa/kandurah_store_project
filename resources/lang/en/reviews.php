<?php

return [
    'title' => 'Reviews Management',
    'subtitle' => 'View and manage customer reviews',
    
    'stats' => [
        'total' => 'Total Reviews',
        'approved' => 'Approved',
        'pending' => 'Pending Review',
        'rejected' => 'Rejected',
        'average' => 'Average Rating',
    ],
    
    'filters' => [
        'search' => 'Search',
        'search_placeholder' => 'Search by customer name or comment...',
        'status' => 'Status',
        'all_statuses' => 'All Statuses',
        'rating' => 'Rating',
        'all_ratings' => 'All Ratings',
        'apply' => 'Apply Filters',
        'reset' => 'Reset',
    ],
    
    'rating' => [
        '5_stars' => '⭐⭐⭐⭐⭐ 5 Stars',
        '4_stars' => '⭐⭐⭐⭐ 4 Stars',
        '3_stars' => '⭐⭐⭐ 3 Stars',
        '2_stars' => '⭐⭐ 2 Stars',
        '1_star' => '⭐ 1 Star',
    ],
    
    'status' => [
        'pending' => 'Pending Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'active' => 'Active',
    ],
    
    'actions' => [
        'view_order' => 'View Order',
        'view_profile' => 'Profile',
        'delete' => 'Delete',
    ],
    
    'order' => [
        'order' => 'Order',
        'date' => 'Date',
    ],
    
    'empty' => [
        'title' => 'No Reviews',
        'description' => 'No reviews found matching the search criteria',
    ],
    
    'messages' => [
        'delete_confirm' => 'Are you sure you want to delete this review? This action cannot be undone.',
        'delete_error' => 'An error occurred while deleting the review',
        'delete_success' => 'Review deleted successfully',
    ],
];
