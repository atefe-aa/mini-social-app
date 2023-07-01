<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Profile">
  <div class="list-group">
    @foreach ($posts as $post)
    <x-posts :post="$post" hideAuthor/>
    @endforeach
  </div>
</x-profile>