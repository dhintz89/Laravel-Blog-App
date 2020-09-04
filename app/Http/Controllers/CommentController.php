<?php

namespace App\Http\Controllers;

use App\Comments;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CommentController extends Controller {
    // store: save comment to database
    public function store(Request $request) {
        // on_post, from_user, body
        $input['from_user'] = $request->user()->id;
        $input['on_post'] = $request->input('on_post');
        $input['body'] = $request->input('body');
        $slug = $request->input('slug');
        // create comment in database
        Comments::create( $input );
        return redirect($slug)->with('message', 'Comment published');     
    }
}
