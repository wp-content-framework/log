<?php
/**
 * WP_Framework_Log Views Admin Style Pagination
 *
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var Presenter $instance */
?>
<style>
    #<?php $instance->id();?>-main-contents .pagination {
        margin: 8px 0;
    }

    #<?php $instance->id();?>-main-contents .pagination .pagination-item {
        display: inline-block;
        padding: 2px 10px;
        margin: 2px;
        border: 1px solid #999;
        cursor: default;
        background: #ccc;
    }

    #<?php $instance->id();?>-main-contents .pagination .pagination-now {
        background: #eee;
    }

    #<?php $instance->id();?>-main-contents .pagination a.pagination-item {
        cursor: pointer;
        background: #efefff;
        text-decoration: none;
    }

    #<?php $instance->id();?>-main-contents .pagination a.pagination-item:hover {
        background: #ceceef;
    }
</style>
