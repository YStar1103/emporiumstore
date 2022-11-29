@extends('admin.layout.layout') @section('content')
<?php

$amount = $_REQUEST['totalamount'];
$seller_id = $_REQUEST['seller_id'];
$payment_method = $_REQUEST['payment_method'];
$stripeToken = $_REQUEST['stripeToken'];
?>
@include('front.stripe.Stripe')
<?php
$params = array(
  "testmode"   => "on",
  //"private_live_key" => "sk_live_UUCOI2p5f6ye9oqmIPKgohSW",
  //"public_live_key"  => "pk_live_NM71jfG5jXk2ChKV4PnBW6Uw",
"private_live_key" => "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",
"public_live_key"  => "pk_live_51JX5TNGXLUn7XVFqQLGZQapiH7yBGBhLByOSThmeSaG1xmkZl9uAu4uejbXWKm7pxZFEik0vrU3m8tcQkwCTdLzQ00m1bTCxVN",
  "private_test_key" => "sk_test_51JX5TNGXLUn7XVFqQBfnTV6rErv1estkRcRqmUDhiN5ks9hRmeJJEv7jvKtFBMcnDfDvfusxbCoDTTI0xeBo5dr100itAeFXYw",
  "public_test_key"  => "pk_test_51JX5TNGXLUn7XVFqCbINRWKVDexvu2XWvGMcpOD3fugDi9Yb1YYfYj5yO2P0T6N9oSPs4eHYHFk0Ae9S1hmJClJv00qNvTWw3t"
);

/*if ($params['testmode'] == "on") 
{
Stripe::setApiKey($params['private_test_key']);
$pubkey = $params['public_test_key'];
} 
else 
{*/
Stripe::setApiKey($params['private_live_key']);
$pubkey = $params['public_live_key'];
/*}*/
if(isset($_POST['stripeToken']))
{
$random_str = '1234567890';
$shuffle_str = str_shuffle($random_str);
$invoiceid = substr($shuffle_str,0,8); // Invoice ID
$amount_cents = str_replace(",", "", $amount)*100; // Chargeble amount
$description = "Invoice #" . $invoiceid . " - " . $invoiceid;
try
{
  $charge = Stripe_Charge::create(array(
          "amount" => $amount_cents,
          "currency" => "gbp",
          "source" => $_POST['stripeToken'],
          "description" => $description)
  );
  /*echo "<pre>";
  print_r($charge);die;*/
  /*if ($charge->card->address_zip_check == "fail") 
  {
    echo "Zip Check Invalid";
    throw new Exception("zip_check_invalid");
  }
  else if ($charge->card->address_line1_check == "fail") 
  {
    echo "Address Check Invalid";
    throw new Exception("address_check_invalid");
  } 
  else if ($charge->card->cvc_check == "fail") 
  {
    echo "CVC Check Invalid";
    throw new Exception("cvc_check_invalid");
  }die;*/
  // Payment has succeeded, no exceptions were thrown or otherwise caught
  $result = "success";
}
catch(Stripe_CardError $e) 
{
  $error = $e->getMessage();
  $result = "declined";

}
catch (Stripe_InvalidRequestError $e) 
{
  $result = "declined";
}
catch (Stripe_AuthenticationError $e) 
{
  $result = "declined";
}
catch (Stripe_ApiConnectionError $e) 
{
  $result = "declined";
} 
catch (Stripe_Error $e) 
{
  $result = "declined";
}
catch (Exception $e) 
{
  if ($e->getMessage() == "zip_check_invalid") 
  {
    $result = "declined";
  } 
  else if ($e->getMessage() == "address_check_invalid") 
  {
    $result = "declined";
  } 
  else if ($e->getMessage() == "cvc_check_invalid") 
  {
    $result = "declined";
  } 
  else 
  {
    $result = "declined";
  }
}//die;
if($result=="success")
{

$NewDate=Date('y:m:d', strtotime("+14 days"));
$subscription_start_date = date('Y-m-d');
$subscription_end_date=Date('Y-m-d', strtotime("+14 days"));
$addData=array(
    'seller_id'=>$seller_id,
    'featured_subscription_id' => 1,
    'start_date'=>$subscription_start_date,
    'payment_status'=>$result,
    'start_date'=>$subscription_start_date,
    'end_date'=>$subscription_end_date,
    'amount'=>$amount,
    'duration'=>15,
    'transaction_id'=>$stripeToken,
    'created_at'=>date('Y-m-d')
);
DB::table('seller_featured_subscription')->insert($addData);
$id = DB::getPdo()->lastInsertId();







$featured_subscription = DB::table('seller_featured_subscription')->where('transaction_id',$stripeToken)->orderBy('id','desc')->first();
  $seller_id = $featured_subscription->seller_id;
  $user_data = DB::table('users')->where('id',$seller_id)->first();
  $name = $user_data->name;
  
  $addData = array(
    'payment_method'=>$payment_method,
    'amount'=>$amount,
    'user_id'=>$seller_id,
    'admin_amount'=>$amount,
    'seller_amount'=>0,
    'buyer_amount'=>0,
    'status'=>$result,
    'type'=>4,
    'transaction_id'=>$stripeToken,
    'created_at'=>date('Y-m-d')
  );
  DB::table('transaction_history')->insert($addData);
  $id = DB::getPdo()->lastInsertId();

  $updateSellersData=array(
  'is_featured_seller'=>1,
  );
  DB::table('sellers')->where(array('user_id'=>$seller_id))->update($updateSellersData);
?>
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Manage Subscription</h1>
    </div>
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<!-- Main content -->
<div class="content">
<div class="container-fluid">
<div class="row">
    <div class="col-lg-12">
        <div class="card">
                <div class="card-body">
                <div class="">
                    <section class="pricing-section">
        <div class="container">
            <div class="outer-box">
                <div class="row">
                    <div class="container" style="margin-top:30px;">
                    <div class="row">
                      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                        <div class="check-out" style="text-align:center; color:red; font-size:20px;">Payment Status</div>
                        <br/>
                          <center><span style="font-size: 18px;text-align:center;">Hi, {{$name}}</span></center>
                          <center><span style="font-size: 18px;text-align:center;">Your Transaction ID No: {{$stripeToken}}</span></center>
                          <center><span style="font-size: 18px;text-align:center;">Your Payment Status is <strong>{{$result}}</strong></span></center>
                          <center><span style="font-size: 18px;text-align:center;">Thanks for purchasing featured subscription.</span></center>
                      </div>
                    </div>   
                  </div>
                </div>
            </div>
        <!-- </form> -->
        </div>
    </section>
                </div>
                
            </div>
            <!-- /.card-body -->
        </div>
    </div>
</div>
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /.content -->
@section('script')
@stop @endsection
<?php
} 
else
{
  echo $response = "<div class='text-danger'>Stripe Payment Status : \".$result.</div>";
}
}
?>