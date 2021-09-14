<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Category;
use App\Notifications\AuthorPostApproved;
use App\Notifications\NewPostNotify;
use App\Subscriber;
use App\Tag;
use App\Post;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::latest()->get();
        return view('admin.post.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create',compact('categories','tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        $this->validate($request,[
            'title' => 'required',
            'price' => 'required',
            'size' => 'required',
            'people_capacity' => 'required',
            'image' => 'required',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',
        ]);
        //image_left upload --------------------
        $image = $request->file('image');
        if (isset($image))
        {
            $path = public_path().'/post_image/';
            //upload new file
            $file = $request->image;
            $filename =  uniqid().$file->getClientOriginalName();
            $file->move($path, $filename);

        } else {
            $filename = "default.png";
        }
        $slug = str_slug($request->title);
        $post = new Post();
        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->price = $request->price;
        $post->size = $request->size;
        $post->people_capacity = $request->people_capacity;
        $post->slug = $slug;
        $post->image = $filename;
        $post->body = $request->body;
        if(isset($request->status))
        {
            $post->status = true;
        }else {
            $post->status = false;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->attach($request->categories);
        $post->tags()->attach($request->tags);

//        $subscribers = Subscriber::all();
//        foreach ($subscribers as $subscriber)
//        {
//            Notification::route('mail',$subscriber->email)
//                ->notify(new NewPostNotify($post));
//        }

        Toastr::success('Post Successfully Saved :)','Success');
        return redirect()->back();
//        return redirect()->route('admin.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.post.show',compact('post'));
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit',compact('post','categories','tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $this->validate($request,[
            'title' => 'required',
            'price' => 'required',
            'size' => 'required',
            'people_capacity' => 'required',
            'image' => 'image',
            'categories' => 'required',
            'tags' => 'required',
            'body' => 'required',
        ]);
        $image = $request->file('image');
        $slug = str_slug($request->title);
        if(isset($image))
        {
//            make unipue name for image
            $currentDate = Carbon::now()->toDateString();
            $imageName  = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

            if(!Storage::disk('public')->exists('post'))
            {
                Storage::disk('public')->makeDirectory('post');
            }
//            delete old post image
            if(Storage::disk('public')->exists('post/'.$post->image))
            {
                Storage::disk('public')->delete('post/'.$post->image);
            }
            $postImage = Image::make($image)->resize(1600,1066)->save();
            Storage::disk('public')->put('post/'.$imageName,$postImage);

        } else {
            $imageName = $post->image;
        }

        $post->user_id = Auth::id();
        $post->title = $request->title;
        $post->title = $request->title;
        $post->price = $request->price;
        $post->size = $request->size;
        $post->people_capacity = $request->people_capacity;
        $post->slug = $slug;
        $post->image = $imageName;
        $post->body = $request->body;
        if(isset($request->status))
        {
            $post->status = true;
        }else {
            $post->status = false;
        }
        $post->is_approved = true;
        $post->save();

        $post->categories()->sync($request->categories);
        $post->tags()->sync($request->tags);

        Toastr::success('Post Successfully Updated :)','Success');
        return redirect()->route('admin.post.index');
    }

    public function pending()
    {
//        $posts = Post::where('is_approved',false)->get();
     $posts = Post::select('posts.*','b.*','b.id as booking_id','b.status as b_status','u.*')
                ->join('bookings as b', 'b.post_id','=','posts.id')
                ->join('users as u','u.id','=','b.user_id')
                ->get();
        return view('admin.post.pending',compact('posts'));
    }
    public function bookingViews($id)
    {
        $post = Booking::find($id);
        return view('admin.post.show_booking',compact('post'));
    }
    public function bookingDetails($id){
        $post = Booking::find($id);
        return view('admin.post.show_booking',compact('post'));
    }
    public function bookingApproval($id)
    {
        $post = Booking::find($id);
        if ($post->status == false)
        {
            $post->status = true;
            $post->save();


            Toastr::success('Post Successfully Approved :)','Success');
        } else {
            Toastr::info('This Post is already approved','Info');
        }
        return redirect()->back();
    }
    public function bookingCompleted($id)
    {
        $post = Booking::find($id);
        if ($post->status == 1)
        {
            $post->status = 2;
            $post->save();


            Toastr::success('Post Successfully booking Completed :)','Success');
        } else {
            Toastr::info('This Post is already booking Completed','Info');
        }
        return redirect()->back();
    }
    public function bookingCancel($id)
    {
        $post = Booking::find($id);
        if ($post->status == 1)
        {
            $post->status = 3;
            $post->save();


            Toastr::success('Post Successfully booking Cancel :)','Success');
        }elseif ($post->status == 2)
        {
            $post->status = 3;
            $post->save();


            Toastr::success('Post Successfully booking Cancel :)','Success');
        }

        else {
            Toastr::info('This Post is already booking Cancel','Info');
        }
        return redirect()->back();
    }


    public function approval($id)
    {
        $post = Post::find($id);
        if ($post->is_approved == false)
        {
            $post->is_approved = true;
            $post->save();
            $post->user->notify(new AuthorPostApproved($post));

            $subscribers = Subscriber::all();
            foreach ($subscribers as $subscriber)
            {
                Notification::route('mail',$subscriber->email)
                    ->notify(new NewPostNotify($post));
            }

            Toastr::success('Post Successfully Approved :)','Success');
        } else {
            Toastr::info('This Post is already approved','Info');
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if (Storage::disk('public')->exists('post/'.$post->image))
        {
            Storage::disk('public')->delete('post/'.$post->image);
        }
        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
        Toastr::success('Post Successfully Deleted :)','Success');
        return redirect()->back();
    }
}
