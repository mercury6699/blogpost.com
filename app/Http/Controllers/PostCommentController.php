<?php

namespace App\Http\Controllers;

use App\Events\CommentPosted;
use App\Http\Requests\StoreComment;
use App\Jobs\NotifyUsersPostWasCommented;
use App\Jobs\ThrottledMail;
use App\Mail\CommentPostedMarkdown;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Comment as CommentResource;

class PostCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }

    public function index(BlogPost $post)
    {
        return CommentResource::collection($post->comments()->with('user')->get());
//        return $post->comments()->with('user')->get();
    }

    public function store(BlogPost $post, StoreComment $request)
    {
        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id,
        ]);

        event(new CommentPosted($comment));

        return redirect()
            ->back()
            ->withStatus('Comment was created successfully!');
    }
}

//        Mail::to($post->user)->send(
//            new CommentPostedMarkdown($comment)
//        );
//        $when = now()->addSeconds(20);
//        Mail::to($post->user)->later($when,
//            new CommentPostedMarkdown($comment)
//        );

//        Mail::to($post->user)->queue(
//            new CommentPostedMarkdown($comment)
//        );


