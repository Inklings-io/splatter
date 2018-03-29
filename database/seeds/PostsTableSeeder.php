<?php

use Illuminate\Database\Seeder;
use App\Post;
use App\Interaction;
use App\Context;
use App\ReplyTo;
use App\SyndicationUrl;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0 ; $i < 100; $i ++){
            $post = factory(Post::class)->create();


            if(rand(0,3) == 0){
                factory(SyndicationUrl::class, rand(1,3))->create(['post_id' => $post->id]);
            }

            if($post->type == 'reply' || $post->type){

                factory(ReplyTo::class)->create(['post_id' => $post->id]);
                $context = factory(Context::class)->create();
                $post->contexts()->save($context);
                
                $parent_context = $context;
                for($k = 0 ; $k < rand(-1,4); $k ++){
                    $context2 = factory(Context::class)->create();
                    $parent_context->contexts()->save($context2);
                    $parent_context = $context2;
                }

                // rarely create a second reply-to
                if(rand(0,4) == 0){
                    factory(ReplyTo::class)->create(['post_id' => $post->id]);
                    $context = factory(Context::class)->create();
                    $post->contexts()->save($context);

                    $parent_context = $context;
                    for($k = 0 ; $k < rand(-2,2); $k ++){
                        $context2 = factory(Context::class)->create();
                        $parent_context->contexts()->save($context2);
                        $parent_context = $context2;
                    }

                }
            }


            //add likes, replies, reposts, etc
            for($j = 0 ; $j < rand(0,10); $j ++){
                $interaction = factory(Interaction::class)->create();
                $post->interactions()->save($interaction);

                // add some reply's to any replies, weight toward there being none
                if($interaction->type == 'reply' && rand(0,1) == 1){
                    for($k = 0 ; $k < rand(0,4); $k ++){
                        $interaction2 = factory(Interaction::class)->create();
                        $interaction->interactions()->save($interaction2);
                        if(rand(0,2) == 0){
                            $interaction3 = factory(Interaction::class)->create();
                            $interaction2->interactions()->save($interaction3);

                        }
                    }
                }
            }

        }
        /*factory(Post::class, 100)->create()->each(function ($post) {
            $post->interactions()->save(factory(Interaction::class)->make(rand(0,10)));
        })*/
        //
    }
}
