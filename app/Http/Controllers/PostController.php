<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Posts;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostFormRequest;

class PostController extends Controller {
    
    // index: show latest 5 posts
    public function index() {
        //fetch latest 5 active posts from database
        $posts = Posts::where('active',1)->orderBy('created_at','desc')->paginate(5);
        //page heading
        $title = 'Latest Posts';
        //return home.blade.php template from resources/views folder
        return view('home')->withPosts($posts)->withTitle($title);
    }
    
    // show: return Post.show view for single post
    public function show($slug) {
        // fetch post from database
        $post = Posts::where('slug',$slug)->first();
        // if not found, redirect to home with error
        if(!$post) {
            return redirect('/')->withErrors('requested page not found');
        }
        // fetch associated comments from database
        $comments = $post->comments;
        // return Post.show view
        return view('posts.show')->withPost($post)->withComments($comments);
    }

    //  create: show form for creating post
    public function create(Request $request) {
        //  check permissions (role = author or admin)
        if ($request->user()->can_post()) {
            //  return form
            return view('posts.create');
        } else {
            // redirect to homepage
            return redirect('/')->withErrors('You do not have sufficient permissions for writing post');
        }
    }

    // store: save the post to database  (PostFormRequest contains validations)
    public function store(PostFormRequest $request) {
        // create new Post instance
        $post = new Posts();
        $post->title = $request->get('title');
        $post->body = $request->get('body');
        $post->slug = Str::slug($post->title);
    
        // if duplicate title, redirect back to create with error msg
        $duplicate = Posts::where('slug', $post->slug)->first();
        if ($duplicate) {
            return redirect('new-post')->withErrors('Title already exists.')->withInput();
        }

        // save post to database
        $post->author_id = $request->user()->id;
        if ($request->has('save')) {
            $post->active = 0;
            $message = 'Post saved successfully';
        } else {
            $post->active = 1;
            $message = 'Post published successfully';
        }
        $post->save();
        // return 
        return redirect('edit/' . $post->slug)->withMessage($message);
    }

    // edit: show form for editing an existing post (might be error due to if statement syntax)
    public function edit(Request $request,$slug) {
        // fetch post from database
        $post = Posts::where('slug', $slug)->first();
        // if post exists and user has correct permissions, return edit form
        if($post && ($request->user()->id == $post->author_id || $request->user()->is_admin()))
            return view('posts.edit')->with('post', $post);
        // if user doesn't have permission, redirect to homepage with errors
        return redirect('/')->withErrors('you do not have sufficient permissions');
    }

    // update: change post attributes in database
    public function update(Request $request) {
        // fetch post from database by id
        $post_id = $request->input('post_id');
        $post = Posts::find($post_id);
        // if post exists and user has correct permissions, update post attributes
        if ($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
            $title = $request->input('title');
            $slug = Str::slug($title);
            // check for duplicate posts
            $duplicate = Posts::where('slug', $slug)->first();
            if ($duplicate) {
                if ($duplicate->id != $post_id) {
                    return redirect('edit/' . $post->slug)->withErrors('Title already exists.')->withInput();
                } else {
                    $post->slug = $slug;
                }
            }

            // update post attributes
            $post->title = $title;
            $post->body = $request->input('body');
    
            // save update post to database
            if ($request->has('save')) {
                $post->active = 0;
                $message = 'Post saved successfully';
                $landing = 'edit/' . $post->slug;
            } else {
                $post->active = 1;
                $message = 'Post updated successfully';
                $landing = $post->slug;
            }
            $post->save();
            // return to landing page
            return redirect($landing)->withMessage($message);
        } else {
            // redirect to home page with error
            return redirect('/')->withErrors('you do not have sufficient permissions');
        }
    }

    // delete: remove from database
    public function destroy(Request $request, $id) {
      // fetch post from db
      $post = Posts::find($id);
      // check permissions
      if($post && ($post->author_id == $request->user()->id || $request->user()->is_admin())) {
        $post->delete();
        $data['message'] = 'Post deleted Successfully';
      } else {
        $data['errors'] = 'Invalid Operation. You do not have sufficient permissions';
      }
      // redirect to homepage
      return redirect('/')->with($data);
    }

}
