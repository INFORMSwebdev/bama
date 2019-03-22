<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/20/2019
 * Time: 2:14 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    $_SESSION['admin_login_redirect'] = "/admin/index.php";
    header( "Location: /users/admin_login.php" );
    exit;
}
$errors = [];

$appStatusNew = APPROVAL_TYPE_NEW;
$appStatusApproved = APPROVAL_TYPE_APPROVE;
$appStatusRejected = APPROVAL_TYPE_REJECT;
$appStatusRetired = APPROVAL_TYPE_RETIRED;
$appStatusDeleted = APPROVAL_TYPE_DELETED;

$content = <<<EOT
<div class="container">
	<div class="flex-column">
		<h1>Analytics & OR Education Database ADMIN</h1>
    </div>
</div>
<div id="searchFormContainer"> 

    <div class="search-wrapper pull">
        <span class="glyphicon glyphicon-search"></span>
        <form id="searchForm">
            <input id="search_term" type="text" class="search-form form-control form-inline" name="search_term" value="" placeholder="Search Term">
            <input type="hidden" name="category" id="category" value="All">
        </form>
        
        <div class="btn-group search-category">
            <button  id="searchContent" class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                <span id="buttonName" data-i18n>All</span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu left" id="searchCategoryMenu" role="menu">
                <li>
                    <a id="searchInstitutions" >
                      <i class="glyphicon glyphicon-search"></i>
                      <span data-i18n>Institutions</span>
                    </a>
                </li>
                <li>
                    <a id="searchColleges">
                        <i class="glyphicon glyphicon-search"></i>
                        <span data-i18n>Colleges</span>
                    </a>
                </li>
            </ul>
        </div>
        <button class="btn" id="btn-launchSearch">Search</button>
    </div>
</div>
<div id="mainContainer"> 

    <form id="filterForm">
        <div class="flex-column form-group" id="statusFilterContainer"> 
        <span class="filter-label">Status Filter:</span>
        <div class="form-check-inline">
          <label class="form-check-label" for="cb-New">
            <input type="checkbox" id="cb-New" name="status[]" value="$appStatusNew" />New
          </label>
        </div>
        <div class="form-check-inline">
          <label class="form-check-label" for="cb-Approved">
            <input type="checkbox" id="cb-Approved" name="status[]" value="$appStatusApproved" checked />Approved
          </label>
        </div>
        <div class="form-check-inline">
          <label class="form-check-label" for="cb-Rejected">
            <input type="checkbox" id="cb-Rejected" name="status[]" value="$appStatusRejected" />Rejected
          </label>
        </div>
        <div class="form-check-inline">
          <label class="form-check-label" for="cb-Retired">
            <input type="checkbox" id="cb-Retired" name="status[]" value="$appStatusRetired" />Retired
          </label>
        </div>
        <div class="form-check-inline">
          <label class="form-check-label" for="cb-Deleted">
            <input type="checkbox" id="cb-Deleted" name="status[]" value="$appStatusDeleted" />Deleted
          </label>
        </div>
        </div> 
    </form>
    
    <div id="searchResultsContainer"> 
        <table id="resultTable" class="table-striped">
          <thead> 
            <tr><th>Category</th><th>Name</th><th>Action</th></tr>
          </thead>
          <tbody> 
          </tbody>
        </table>
        <p><i>NOTE: The search engine in use here throws out results where the search phrase matches more than 50% of table rows for a given table, which is why searches for a common term 
        like "data" might come up empty for a particular category.</i></p>
    </div>
    
</div>
EOT;

$custom_css = <<<EOT
a {
    cursor: pointer;
}
header .dropdown-menu {
    top: 46px;
    border: 0;
}
#searchForm {
    order: 2;
    flex: 1;
}
.glyphicon, .icon {
    position: relative;
    display: inline-block;
    font-family: "Fontello";
    top: initial;
    line-height: unset;
}
.search-wrapper {
    position: relative;
    margin: 20px auto;
    padding: 0;
    display: flex;
    width: 67%;
}
.search-wrapper>.glyphicon-search {
    top: 1px;
    padding: 10px;
    font-size: 13px;
    position: absolute;
    z-index: 10;
}
.search-category .dropdown-menu a {
    padding-top: 8px;
    padding-bottom: 8px;
    padding-left: 12px;
    display: block;
}
#statusFilterContainer {
    position: relative;
    display: flex;
}
#mainContainer {
    display: grid;
    grid-template-columns: 150px auto;
    min-height: 200px;
    padding: 10px;
}
#searchContent {
    background-color: #092f87;
    color: #fff;
    min-width: 133px;
}
#btn-launchSearch {
    background-color: #80bd01;
    color: #fff;
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
}
EOT;


$custom_js = <<<EOT
var categories = [ 'All', 'Institutions', 'Colleges', 'Programs', 'Contacts', 'Courses', 'Instructors', 'Textbooks', 'Software', 'Datasets', 'Case Studies' ];
var currentCat = 'All';
function createSearchCategoryMenu( label ) {
  $('#searchCategoryMenu').html('');
  $('#category').val( label );
  $('#buttonName').html( label );
  for( var i=0; i < categories.length; i++) {
    if (label != categories[i]) {
      var li = $('<li><a><i class="glyphicon glyphicon-search"></i><span>'+categories[i]+'</span></a></li>');
      $('#searchCategoryMenu').append( li );
    }
  }
}
function launchSearch() {
    var data = $('#searchForm, #filterForm').serialize();
    resultTable.ajax.url( '/scripts/ajax_adminSearchProcess.php?'+data ).load();
}
var resultTable = '';
$(function() {
    createSearchCategoryMenu( 'All' );
    resultTable = $('#resultTable').DataTable({
        "order": [[3, 'desc']],
        "columnDefs": [
            {
                "targets": 0,
                "data": "category"
            },
            {
                "targets": 1,
                "data": "name"
            },
            {
                "targets": 2,
                "data": "id",
                "orderable": false, 
                "className": "ctrl-col",
                "render": function ( data, type, row, meta ) {
                    var oClass = (row.category=='Case Studies') ? 'cases' : row.category.toLowerCase();
                    var link = '<a href="/'+oClass+'/display.php?id='+data + '">View</a>';
                    return link;
                }
            },
            {
                "targets": 3,
                "data": "score",
                "visible": false
            }
        ]
  });
  $(document).on( 'click keyup', '.search-category .dropdown-menu a', function(e) {
    var label = $(this).find( 'span' )[0].innerHTML;
    createSearchCategoryMenu( label );
  });
  $('#btn-launchSearch').on( 'click keyup', function(e) {
    launchSearch();
  });
  $('#searchForm').on( 'submit', function(e) {
    e.preventDefault();
    launchSearch();
  });
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();