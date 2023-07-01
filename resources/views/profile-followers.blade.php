<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Followers">
    <div class="list-group">
      @foreach ($followers as $follower)
        <a href="/profile/{{$follower->userDoingTheFollowing->username}}" class="list-group-item list-group-item-action">
            <img class="avatar-tiny" src="{{$follower->userDoingTheFollowing->avatar}}" />
            {{$follower->userDoingTheFollowing->username}}
        </a>
      @endforeach
     
    </div>
  </x-profile>