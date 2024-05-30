<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::orderBy('id','DESC')->paginate(20);
        return view('admin.product.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cats = Category::orderBy('name','ASC')->select('id','name')->get();
        return view('admin.product.create', compact('cats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4|max:150|unique:products',
            'description' => 'required|min:4',
            'price' => 'required|numeric',
            'sale_price' => 'numeric|lte:price',
            'img' => 'required|file|mimes:jpg,jpeg,png,gif',
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $request->only('name','price','sale_price','status','description','category_id');

        $imag_name = $request->img->hashName();
        $request->img->move(public_path('uploads/product'), $imag_name);
        $data['image'] = $imag_name;

        if ($product = Product::create($data)) {

            if($request->has('other_img')) {
                foreach($request->other_img as $img){
                    $other_name = $img->hashName();
                    $img->move(public_path('uploads/product'), $other_name);

                    ProductImage::create([
                        'image' => $other_name,
                        'product_id' => $product->id
                    ]);
                }
            }

            return redirect()->route('product.index')->with('ok','Create new product successffuly');
        }



        return redirect()->back()->with('no','Something error, Please try again');


    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $cats = Category::orderBy('name','ASC')->select('id','name')->get();
        return view('admin.product.edit', compact('cats','product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|min:4|max:150|unique:products,name,'.$product->id,
            'description' => 'required|min:4',
            'price' => 'required|numeric',
            'sale_price' => 'numeric|lte:price',
            'img' => 'file|mimes:jpg,jpeg,png,gif',
            'category_id' => 'required|exists:categories,id'
        ]);

        $data = $request->only('name','price','sale_price','status','description','category_id');

        if ($request->has('img')) {
            $img_name = $product->image;
            
            $image_path = public_path('uploads/product').'/'.$img_name;
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            $imag_name = $request->img->hashName();
            $request->img->move(public_path('uploads/product'), $imag_name);
            $data['image'] = $imag_name;
        }

        if ($product->update($data)) {

            if($request->has('other_img')) {

                if ($product->images->count() > 0) {
                    foreach($product->images as $img) {
                        $othe_image = $img->image;
                        $other_path = public_path('uploads/product').'/'.$othe_image;
                        if (file_exists($other_path)) {
                            unlink($other_path);
                        }
                    }

                    ProductImage::where('product_id', $product->id)->delete();
                }

                foreach($request->other_img as $img){
                    $other_name = $img->hashName();
                    $img->move(public_path('uploads/product'), $other_name);

                    ProductImage::create([
                        'image' => $other_name,
                        'product_id' => $product->id
                    ]);
                }
            }
            return redirect()->route('product.index')->with('ok','UIpdate the product successffuly');
        }

        return redirect()->back()->with('no','Something error, Please try again');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $img_name = $product->image;
        $image_path = public_path('uploads/product').'/'.$img_name;

        if ($product->images->count() > 0) {
            foreach($product->images as $img) {
                $othe_image = $img->image;
                $other_path = public_path('uploads/product').'/'.$othe_image;
                if (file_exists($other_path)) {
                    unlink($other_path);
                }
            }

            ProductImage::where('product_id', $product->id)->delete();

            if ($product->delete()) {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }

                return redirect()->route('product.index')->with('ok','Delete product successffuly');
            }

        } else {

            if ($product->delete()) {
                if (file_exists($image_path)) {
                    unlink($image_path);
                }

                return redirect()->route('product.index')->with('ok','Delete product successffuly');
            }
        }


        return redirect()->back()->with('no','Something error, Please try again');
    }

    public function destroyImage(ProductImage $image)
    {
        $img_name = $image->image;
        if ($image->delete()) {
            $image_path = public_path('uploads/product').'/'.$img_name;
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            return redirect()->back()->with('ok','Delete Image successffuly');
        }

        return redirect()->back()->with('no','Something error, Please try again');

    }
}
