<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
class FrontController extends Controller
{

    public function contactus(Request $req)
    {
        if($req->isMethod('post'))
        {
            $this->from=strtolower($req->input('email'));
            
            $dataset=array('name'=>$req->input('name'),
            'email'=>strtolower($req->input('email')),
            'phone'=>$req->input('phone'),
            'subject'=>$req->input('subject'),
            'message'=>$req->input('message'));
            $res=  Mail::send('front/sendmail/contact_email',$data =
            [
            'dataset'=>$dataset 
            ],function($message){
                // return $data;
                $message->from($this->from,'KEEP IN TOUCH');
                $message->to('djsaluja18@gmail.com','KEEP IN TOUCH');
                $message->subject('KEEP IN TOUCH');
            });
            //session()->flash('success','Successfully send your enquiry. Thank you.');
            //return redirect(url()->previous().'#contact'); 
            return back()->with('success','Successfully send your enquiry. Thank you.');        
        }

        //return view('/front/contact-us');
        $contact = DB::table('page_content')->where('page_id',2)->orderBy('id','asc')->get();
        return view('/front/contact-us',['contact'=>$contact]);
        //return back()->with('success','Successfully send your enquiry. Thank you.');
    }

   

    public function product_list(Request $req)
    {
        if($req->isMethod('post'))
        {
        $type=$req->type;
        $search_no=$req->search_no;
        $product;
        if($type==0)
        {
           $product=DB::table('products')->where('productnumber','Like',"%{$search_no}%")->where('status',1)->orderBy('id','desc')->paginate(9);  
        }
        if($type==1)
        {
            $product=DB::table('products')->where('productnumber','Like',"%{$search_no}")->where('status',1)->orderBy('id','desc')->paginate(9);  
        }
        if($type==2)
        {
            $product=DB::table('products')->where('productnumber','Like',"{$search_no}%")->where('status',1)->orderBy('id','desc')->paginate(9);  
        }
        $cate=DB::table('category')->orderBy('id','desc')->get();
        return view('/front/products',['product'=>$product,'cate'=>$cate,'type'=>$type,'search_no'=>$search_no]);
        }
        
        $product=DB::table('products')->where('status',1)->orderBy('id','desc')->paginate(9);
        $cate=DB::table('category')->orderBy('id','desc')->get();
        return view('/front/products',['product'=>$product,'cate'=>$cate]);
    }
    public function aboutus()
    {
        //return view('/front/about-us');
        $about = DB::table('pages')->where('page_name','About us')->orderBy('id','desc')->first();
        return view('/front/about-us',['about'=>$about]);
    }
    public function cookies()
    {
        return view('/front/cookies');
    }
    public function seller_login()
    {
        return view('/front/seller-login');
    }
    public function welcome_seller()
    {
        return view('/front/welcome-seller');
    }
    public function product_detail()
    {
        return view('/front/product-detail');
    }
    public function privacypolicy()
    {
        return view('/front/privacy-policy');
    }
    public function faq()
    {
        return view('/front/faq');
    }
    public function terms()
    {
        return view('/front/terms');
    }
    public function registeration(Request $req)
    {
        $user_type = $req->input('user_type');
        if($user_type==3)
        {
            $buyer = 'BUYER';
            $random_str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $shuffle_str = str_shuffle($random_str);
            $u_id = $buyer.substr($shuffle_str,0,6);
            $is_admin = 2;
        }
        else
        {
            $seller = 'SELLER';
            $random_str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $shuffle_str = str_shuffle($random_str);
            $u_id = $seller.substr($shuffle_str,0,6);
            $is_admin = 3;
        }
        if($req->isMethod('post'))
        {
            $this->validate($req,
            [
            'email'=>'unique:users,email',
            'password' => 'required',
            //'cpassword'=>'required_with:password|same:password'

            ],
            [
                'email.unique'=>'Email Already Exist',
                //'cpassword.same'=>'Confirm password mismatch'
            ]);
     $addData=array(
         'u_id'=>$u_id,
         'is_admin'=>$is_admin, 
         'user_type'=>$req->input('user_type'),
         'email'=>$req->input('email'),
         //'phone'=>$req->input('phone'),
         'password'=>md5($req->input('password')),
         //'cpassword'=>$req->input('cpassword'),
         'status'=>1,
         'created_at'=>date('Y-m-d')
            ); 
     print_r($addData);die;
            $userid=DB:: table('users')->insertGetId($addData);
            //$userid=DB:: table('userregister')->insertGetId($addData);
            //return $userid;
            //return redirect('/create-ehr/'.$userid);
            //$this->emailverification($userid);
            return back()->with('success','Successfully created account'); 
        }
        return view('front/registeration');
    } 
    
    function productdetail($slug){        
        //return $slug;
        $product = DB::table('products')->where('slug',$slug)->first();
        $product_id[] = $product->id;
        $category_id = $product->catid;
        $subcategory_id = $product->subcat_id;
        $productdetail=DB::table('products')->where('id',$product_id)->first();
        $similarproduct = DB::table('products')->where(array('catid'=>$category_id,'status'=>1))->whereNotIn('id', $product_id)->get();
        $boughttogetherproduct = DB::table('products')->where(array('catid'=>$category_id,'subcat_id'=>$subcategory_id,'status'=>1))->get();
        $recentlyviewed = '';
        //$recentlyviewed = DB::table('products')->where('views','>', 50)->where('status', 1)->take(2)->get();
        //$variantdetail=DB::table('product_detail')->where('product_id',$product_id)->get();
        return view('front/product-detail',['recentlyviewed'=>$recentlyviewed,'boughttogetherproduct'=>$boughttogetherproduct,'similarproduct'=>$similarproduct,'productdetail'=>$productdetail]);
    }

    // filter product by category
    function filterproduct($slug)
    {
        // return $slug;
        $cat=DB::table('category')->where('cat_slug',$slug)->first();
        // return $cat->id;
        $filter=DB::table('products')->where(array('catid'=>$cat->id,'status'=>1))->orderBy('id','desc')->paginate(9);
        $cate=DB::table('category')->orderBy('id','desc')->get();
        return view('/front/products',['product'=>$filter,'cate'=>$cate]);
    }

    /*function productlist($slug)
    {

        echo $a = "SELECT cat.id, cat.slug FROM category as cat, sub_category as sub_cat WHERE cat.slug = $slug";die;
    }*/

    // product by category
    function categoryproduct($slug)
    {
        if(!empty($_REQUEST))
        {
            $query = DB::table('product_detail');
            //dd($query);die;
            foreach ($_REQUEST as $key=>$val) 
            {
                foreach ($val as $k => $v) {
                //echo"<br>".$v;
                //$new[]= "spec_detail LIKE '%".$v."%'";
                if($v)
                {
                    $query->orWhere('spec_detail','LIKE','%"'.$v.'"%');
                }
                }
            }
            $result = $query->get();
            $addsubqry='';
            $newproduct_id='';
            foreach ($result as $productresult) {
                $productid[] = $productresult->product_id;
                $newproduct_id = implode(',', $productid);
            }
            $category = DB::table('category')->where('slug',$slug)->first();
            //echo $addsubqry .= " AND catid=$category->id AND id IN ($newproduct_id)";
            //echo "select * from products where status=1 $addsubqry";
            $newproductid = explode(',',$newproduct_id);
            $categoryproduct = DB::table('products')->where(array('status'=>1,'catid'=>$category->id))->whereIn('id', $newproductid)->orderBy('id','desc')->paginate(12);
            $cate = DB::table('category')->orderBy('id','desc')->get();
            $subcategory = DB::table('sub_category')->where('cat_id',$category->id)->orderBy('id','desc')->get();
        }
        else
        {
            $category = DB::table('category')->where('slug',$slug)->first();
            $categoryproduct = DB::table('products')->where(array('catid'=>$category->id,'status'=>1))->orderBy('id','desc')->paginate(12);
            $cate = DB::table('category')->orderBy('id','desc')->get();
            $subcategory = DB::table('sub_category')->where('cat_id',$category->id)->orderBy('id','desc')->get();
        }
        return view('/front/category',['category'=>$category,'categoryproduct'=>$categoryproduct,'cate'=>$cate,'subcategory'=>$subcategory]);
    }

    function subcategoryproduct($slug)
    {
        if(!empty($_REQUEST))
        {
            $query = DB::table('product_detail');
            foreach ($_REQUEST as $key=>$val) 
            {
                foreach ($val as $k => $v) 
                {
                    if($v)
                    {
                        $query->orWhere('spec_detail','LIKE','%"'.$v.'"%');
                    }
                }
            }
            $result = $query->get();
            $addsubqry='';
            $newproduct_id='';
            foreach($result as $productresult) 
            {
                $productid[] = $productresult->product_id;
                $newproduct_id = implode(',', $productid);
            }
            $subcategories = DB::table('sub_category')->where('slug',$slug)->first();
            $cat_id = $subcategories->cat_id;
            $category = DB::table('category')->where('id',$cat_id)->first();
            //echo $addsubqry .= " AND catid=$category->id AND id IN ($newproduct_id)";
            //echo "select * from products where status=1 $addsubqry";
            $newproductid = explode(',',$newproduct_id);
            $subcategoryproduct = DB::table('products')->where(array('catid'=>$cat_id,'subcat_id'=>$subcategories->id,'status'=>1))->whereIn('id', $newproductid)->orderBy('id','desc')->paginate(12);
            $subcate = DB::table('sub_category')->orderBy('id','desc')->get();
            $subcategory = DB::table('sub_category')->where('cat_id',$cat_id)->orderBy('id','desc')->get();
        }
        else
        {
            $subcategories = DB::table('sub_category')->where('slug',$slug)->first();
            $cat_id = $subcategories->cat_id;
            $category = DB::table('category')->where('id',$cat_id)->first();
            $subcategoryproduct = DB::table('products')->where(array('catid'=>$cat_id,'subcat_id'=>$subcategories->id,'status'=>1))->orderBy('id','desc')->paginate(12);
            $subcate = DB::table('sub_category')->orderBy('id','desc')->get();
            $subcategory = DB::table('sub_category')->where('cat_id',$cat_id)->orderBy('id','desc')->get();
        }
        return view('/front/subcategory',['subcategories'=>$subcategories,'subcategoryproduct'=>$subcategoryproduct,'subcate'=>$subcate,'category'=>$category,'subcategory'=>$subcategory]);
    }

    function addwishlist()
    {

        $user_session = session('logged_user');
        $user_id = $user_session->id;

        //echo $user_id = auth()->user()->id;//die;
        if(isset($_REQUEST['product_id']) && $_REQUEST['product_id']!="")
        {
            $product_id = $_REQUEST['product_id'];
            $query_chkdup = DB::table('wishlist')->where('product_id',$product_id)->where('user_id',$user_id)->get();
            $count = count($query_chkdup);
            //dd($query_chkdup);die;
            if($count>0)
            {
                echo $msg = "Product Already Present in your Wishlist!";
                //return back()->with('error','Product Already Present in your Wishlist!');
            }
            else
            {
                $addData=array(
                'user_id'=>$user_id,
                'product_id'=>$product_id,
                'addeddate'=>date('Y-m-d H:i:s'),
                'status'=>1
                );
                DB::table('wishlist')->insert($addData);
                echo $msg = "Product Successfully Added to Wishlist!";
                //return back()->with('success','Product Successfully Added to Wishlist!');
            }
        }
        else
        {
            echo $msg = "Product ID does not matched!";
            //return back()->with('error','Product ID dose not matched!');
        }
    }



    // filter product by price
    function filterproductprice($price)
    {
        $pricerang=explode('-',$price);
        $start=$pricerang[0];
        if($start==500000)
        {
          $end=0; 
        }
        else{ $end=$pricerang[1];}
        
       
        
        
        // $cat=DB::table('category')->where('cat_slug',$slug)->first();
        // return $cat->id;
        $filter;
        if($start==500000)
        {
            $filter=DB::table('products')->where('price', '>=',$start)->where('status',1)->orderBy('id','desc')->paginate(9); 
        }
        else{
        $filter=DB::table('products')->where('price', '>=',$start)->where('price','<=',$end)->where('status',1)->orderBy('id','desc')->paginate(9);
        }
        //return $filter;
        $cate=DB::table('category')->orderBy('id','desc')->get();
        return view('/front/products',['product'=>$filter,'cate'=>$cate]);
    }
    
     function emailverification($userid)
    {
        $user=DB::table('userregister')->where('id',$userid)->first();
        $this->to=strtolower($user->email);
            $dataset=array('id'=>$user->id);
        $res=  Mail::send('front/sendmail/regmail',$data =
        [
        'dataset'=>$dataset 
        ],function($message){
            // return $data;
            $message->from('djsaluja18@gmail.com','Log Zero Technologies');
            $message->to($this->to,'Email Vertification');
            $message->subject('Email Vertification');
        });
        return back()->with('success','Successfully Created account and send activation link on email'); 
    }

    function verifiedemail($id)
    {
        DB::table('userregister')->where('id',$id)->update(array('status'=>1));
        //return view('front/login');
        return redirect('/login')->with('success','Your email successfully verified.');
    }
    
    function search(Request $req)
    {
        // return $req->all();
        $searchdata=$req->searchdata;
        $product=DB::table('products')->where('status',1)->where('productnumber','LIKE', "%{$searchdata}%")->get();
        // return $product;
        return view('front/search',['product'=>$product]);
    }
    
    function filter_seach(Request $req)
    {
        $type=$req->type;
        $search_no=$req->search_no;
        $filter;
        if($type==0)
        {
            $filter=DB::table('products')->where('productnumber','Like',"%{$search_no}%")->where('status',1)->orderBy('id','desc')->get();  
        }
        if($type==1)
        {
           $filter=DB::table('products')->where('productnumber','Like',"%{$search_no}")->where('status',1)->orderBy('id','desc')->get();  
        }
        if($type==2)
        {
           $filter=DB::table('products')->where('productnumber','Like',"{$search_no}%")->where('status',1)->orderBy('id','desc')->get();  
        }
        $cate=DB::table('category')->orderBy('id','desc')->get();
        return view('/front/products',['product'=>$filter,'cate'=>$cate,'type'=>$type,'search_no'=>$search_no]);
    }
    
}
