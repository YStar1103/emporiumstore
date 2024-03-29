@include('front.include.header')
@yield('header')   
<?php
use App\Review;
$category_id = $category->id;
$subcategory_id = $subcategories->id;
$subcategory_name = $subcategories->name;
$category_name = $category->catname;
$category_slug = $category->slug;
?>
<!-- content area start -->
<div class="h-50px d-none d-lg-block"></div>
<div class="container">
<div class="row">
<div class="col-lg-3" id="product_aside_bar">
<aside id="product_aside">
<div class="close_aside_toggle cusror-pointers float-end d-lg-none my-4">
<i data-feather="x-circle"></i>
</div>
<div class="w-100 clearfix"></div>
<div class="related_cat d-none d-lg-block text-black">
<h3 class="ft-20 lh-30 text-black">Related Categories</h3>
<div class="parent_submenu text-black">
<a href="#" class="ft-18 lh-27"><i data-feather="chevron-left"></i> {{$category_name}}</a>
<ul class="list-unstyled ft-15 lh-22 text-secondary">
<?php
$active = '';
if($subcategory)
{
foreach ($subcategory as $allsubcategories) 
{
    if($allsubcategories->name==$subcategory_name)
    {
        $active = "active";
    }
?>
<li><a href="{{$allsubcategories->slug}}" class="ft-bold <?php echo $active;?>">{{$allsubcategories->name}}</a></li>
<?php
}
}
?>

</ul>
</div>
</div>

<div id="filters">
<form name="sortbyfrm" id="sortbyfrm" method="get">
<div class="accordion" id="accordion_prod">
<?php
$a = 0;
//$specifications = DB::table('specifications')->where('cat_id',$category_id)->get();
$specifications = DB::table('specifications')
->select('*')
->whereRaw('FIND_IN_SET('.$category_id.',cat_id)')
->get();
foreach ($specifications as $allspecifications) 
{
    $a++;
    $spec_id = $allspecifications->id;
?>
<div class="accordion-item  border-0 OperatingSystem_filter checkbox_filter">
<h2 class="accordion-header" id="heading<?php echo $a;?>">
<button class="accordion-button collapsed ft-20 lh-30 ft-500" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $a;?>" aria-expanded="false" aria-controls="collapse<?php echo $a;?>">
{{$allspecifications->name}}
<i data-feather="minus"></i>
<i data-feather="plus"></i>
</button>
</h2>
<div id="collapse<?php echo $a;?>" class="accordion-collapse collapse ps-4 text-secondary" aria-labelledby="heading<?php echo $a;?>">
<div class="accordion-body">
<div class="checkbox_filter_listing">
<?php
$aa="";
$aas = array();
$type="";
$type=isset($_REQUEST['sortby']);
if($type=="")
{
foreach ($_GET as $key => $value) {
    if($key!="page"){
	foreach ($value as $k => $val) {
		$aas[] = $val;	
	}
    }
}
}
$options = DB::table('options')->where('specs_id',$spec_id)->get();
foreach ($options as $key => $alloptions) 
{
$option_name = $alloptions->name;
if(in_array($option_name,$aas))
{
$checked ="checked ";
}
else
{
$checked ="";
}
?>
<div class="checkbox">
<label for=" ">
    <input type="checkbox" <?php echo $checked;?> onchange="javascript:document.getElementById('sortbyfrm').submit();" id="{{$allspecifications->name}}" name="{{$allspecifications->name}}[]" value="{{$option_name}}">
    <span>{{$option_name}}</span>
</label>
</div>
<?php
}
?>
</div>
</div>
</div>
</div>
<?php
}

?>
</div>
</form>
</div>


</aside>
</div>
<div class="col-lg-9 ps-lg-5">
<div class="aside_toggle cursor-pointer my-4 d-lg-none">
<i data-feather="arrow-right-circle"></i> Advance Filter
</div>
<div class="breadcrumb_box">
<nav aria-label="breadcrumb">
<ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
<li class="breadcrumb-item"><a href="{{url('category/'.$category_slug)}}">{{$category_name}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{$subcategory_name}}</li>
</ol>
</nav>
</div>
<form id="filterbyfrm" name="filterbyfrm" method="get">
<!--<div class="price_filter ft-12">
<div class="d-flex align-items-center">
<span class="labels">Price</span>
<div class="min_box">
<input type="text" placeholder="min">
</div>
<div class="minus_sep">
<i data-feather="minus"></i>
</div>
<div class="max_box">
<input type="text" placeholder="max">
</div>
</div>
</div>-->
<div class="sortby_filter ft-12">
<div class="d-flex  align-items-center justify-content-between flex-wrap">
<div class="d-flex align-items-center my-4">
<span class="labels">Sort By</span>
<div class="border radius-5">
<div class="d-flex sortby_filter_listing">
<!--<div class="popular_filter">Popular</div>-->
<div class="newset_filter" >
<select class="newset_filter" name="sortbyorder" id="sortbyorder" onchange="javascript:document.getElementById('filterbyfrm').submit();">
<!--<option value="">Sort By Order</option>-->
<option value="asc" <?php if(isset($_REQUEST['sortbyorder']) && $_REQUEST['sortbyorder'] == "asc") { echo "SELECTED"; } ?> >Oldest</option>
<option value="desc" <?php if(isset($_REQUEST['sortbyorder']) && $_REQUEST['sortbyorder'] == "desc") { echo "SELECTED"; } ?> >Newest</option>
</select>
</div>
<!--<div class="rating_filter">Average Rating</div>-->
<div class="price_sorting">
<select  name="sortby" id="sortby" onchange="javascript:document.getElementById('filterbyfrm').submit();">
<!--<option value="">Sort By Price</option>-->
<option value="lowtohigh" <?php if(isset($_REQUEST['sortby']) && $_REQUEST['sortby'] == "lowtohigh") { echo "SELECTED"; } ?> >Low To High</option>
<option value="hightolow" <?php if(isset($_REQUEST['sortby']) && $_REQUEST['sortby'] == "hightolow") { echo "SELECTED"; } ?> >High To Low</option>
</select>
</div>
</div>
</div>
<span class="labels" style="margin-left: 50px">Count: </span>
<div class="border radius-5">
    <div class="d-flex sortby_filter_listing">
    <div class="price_sorting">
        <select name="paginationCount" id="pagination-count" style="border: none;" onchange="javascript:document.getElementById('filterbyfrm').submit();">
            <!--<option value="">Sort By Price</option>-->
            <option value="50" <?php if(isset($_REQUEST['paginationCount']) && $_REQUEST['paginationCount'] == "50") { echo "SELECTED"; } ?> >50</option>
            <option value="75" <?php if(isset($_REQUEST['paginationCount']) && $_REQUEST['paginationCount'] == "75") { echo "SELECTED"; } ?> >75</option>
            <option value="100" <?php if(isset($_REQUEST['paginationCount']) && $_REQUEST['paginationCount'] == "100") { echo "SELECTED"; } ?> >100</option>
        </select>
    </div>
    </div>
</div>
</div>
<div class="d-none d-md-flex gap-3 view_filter my-4 ms-auto">
<div class="border p-2 list_filter cursor-pointer">
<i data-feather="list"></i>
</div>
<div class="border p-2 grid_filter active cursor-pointer">
<i data-feather="grid"></i>
</div>
</div>
</div>
</div>
</form>
<!-- product listing -->
<div class="product_grid product_wrapper">

<div class="row">
<div id="loadmsgpromo" class="alert alert-primary alert_hideo">
</div>
<?php
$subcategoryproductcount = count($subcategoryproduct);
if($subcategoryproductcount>0)
{
foreach($subcategoryproduct as $subcategory_product_result)
{
$product_id = $subcategory_product_result->id;
?>
<div class="col-lg-3 col-md-4 col-6">
<div class="products_unit">
<div class="image">
<a href="{{url('product-detail/'.$subcategory_product_result->slug)}}"><img src="{{ asset('public/products/'.$subcategory_product_result->image) }}" class="img-fluid" alt=""></a>
</div>
<div class="products_desc">
<div class="p_wishlist">
<?php
if(Auth::check())
{
$user_id = auth()->user()->id;
?>
<style>
[type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
cursor: pointer;
background-color: #fff;
border: none;
}
.fa{
    color:red;
}
</style>
<button title="Add to Wish List" data-toggle="tooltip" type="button" class="btn-wishlist" href="javascript:void(0);" onClick="addwishlist('<?php echo $subcategory_product_result->id; ?>');">
    <?php
    $wishlist = DB::table('wishlist')->where(array('user_id'=>$user_id,'status'=>1,'product_id'=>$subcategory_product_result->id))->get();
    $countwishlist = count($wishlist);
    if($countwishlist>0)
    {
    ?>
    <i class="fa fa-heart"></i>
    <?php
    }
    else
    {
    ?>
    <i class="far fa-heart"></i>
    <?php
    }
    ?>
    </button>
<?php
}
else
{
?>
<style>
button.btn-wishlist{
    background-color:#fff;
    border:none;
}
</style>
<button title="Wish List" data-toggle="tooltip" type="button" class="btn-wishlist" href="javascript:void(0);" onClick="wishlist();"><i class="fa fa-heart"></i></button>
<?php
}
?>
</div>
<div class="p_name">
<a href="{{url('product-detail/'.$subcategory_product_result->slug)}}">{{$subcategory_product_result->name}}</a>
</div>
<div class="p_rating d-flex align-items-center gap-2">
@php
$rating_data = Review::getRatingAverage($subcategory_product_result->id);
@endphp
<div class="ratingofprod">
<div class="ratinga2">
    <div class="ratinga" data-value="{{$rating_data['rating']}}"></div>
</div>
</div> 
<span>({{$rating_data['reviews']}})</span>
</div>

<div class="p_price">
<i class="fas fa-pound-sign"></i> {{$subcategory_product_result->price}}
</div>
<?php
$checkCashback = DB::table('cashbacks')->select('*')->where('catid',$category_id)->whereRaw('FIND_IN_SET('.$product_id.',product_id)')->get();
foreach ($checkCashback as $cashback_res) {
$cashbackpercent = $cashback_res->cashback;
?>
<div class="p_offer">
{{$cashbackpercent}}% Cashback
</div>
<?php
}
?>
</div>
</div>
</div>
<?php
}
}
else
{
?>
<div style="text-align: center;" class="alert alert-danger">No product found.</div>
<?php
}
?>
</div>
</div>
<!-- product listing end -->
<!-- pagination -->
<br>
<br>
<hr>
{{-- Pagination --}}
<div class="d-flex justify-content-center">
{!! $subcategoryproduct->links() !!}
</div>
<br>
<br>
<!-- pagination end-->
</div>
</div>
</div>
<style>
select {
    border: none;
}
</style>
<div class="h-50px "></div>
@include('front.include.footer')
@yield('footer')