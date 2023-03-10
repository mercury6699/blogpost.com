<?php

namespace App\Services;

//use Illuminate\Support\Facades\Cache;
use App\Contracts\CounterContract;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Contracts\Session\Session;

class Counter implements CounterContract
{
    private $timeout;
    private $cache;
    private $session;
    private $supportsTags;

    public function __construct(Cache $cache, Session $session, int $timeout)
    {
        $this->timeout = $timeout;
        $this->cache = $cache;
        $this->session = $session;
        $this->supportsTags = method_exists($cache, 'tags');
    }

    public function increment(string $key, array $tags = null): int
    {
        $sessionId = session()->getId();
        $counterKey = "$key-counter";
        $usersKey = "$key-users";

        $cache = $this->supportsTags && null !== $tags
            ? $this->cache->tags($tags) : $this->cache;

        $users = $cache->get($usersKey,[]);
        $usersUpdate = [];
        $difference = 0;
        $now = now();



        foreach ($users as $session => $lastVisit)
        {
            if ($now->diffInMinutes($lastVisit) >= $this->timeout)
            {
                $difference--;
            } else
            {
                $usersUpdate[$session] = $lastVisit;
            }
        }
        if (
            !array_key_exists($sessionId, $users)
            || $now->diffInMinutes($users[$sessionId]) >= $this->timeout
        ){
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;
        $cache->forever($usersKey, $usersUpdate);

        if ($cache->has($counterKey))
        {
            $cache->increment($counterKey, $difference);
        }else {
            $cache->forever($counterKey, 1);
//            dd(Cache::tags(['blog-post'])->get($counterKey));
        }

        $counter = $cache->get($counterKey);

        return $counter;
    }

}
