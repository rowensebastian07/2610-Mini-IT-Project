

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLUBS</title>
    <link rel="stylesheet" href="{{ asset('css/navigation-page.css') }}">
</head>

<body>
   <header>
    <img src="{{ asset('images/csrw-placeholder-2.jpeg') }}" 
         style="width:100%; height:200px; object-fit:cover;">
    <h1>Clubs and Societies in MMU</h1>
   </header>

   <!--  Dynamic Categories Loop -->
@foreach (\App\Enums\ClubCategory::cases() as $category)
   <h2>{{ $category->value }}</h2>
   <div class="container">
       
       {{-- FIX: Check if a user is logged in AND if they are an admin --}}
       @if (auth()->check() && auth()->user()->is_admin)
           
           {{-- Admin View: Shows all clubs, including unverified ones with admin actions --}}
           @foreach ($clubs->where('category', $category->value) as $club)
               <a href="{{ route('clubs.show', $club->id) }}">
                   <p>{{ $club->name }}</p>
                   <img src="{{ asset($club->profile_picture) }}" alt="{{ $club->name }}">
            
                   @if($club->is_Verified == false)
                       <form action="{{ route('clubs.updateVerify', $club->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Verify this club?')" >
                           @csrf
                           @method('PUT')
                           <button type="submit" class="btn-red">Verify Club</button>
                       </form>

                       <form action="{{ route('clubs.destroy', $club->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this club?')" >
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="btn-red">Delete Club</button>
                       </form>
                   @endif
               </a>
           @endforeach
           @else
            @foreach ($clubs->where('category', $category->value) as $club)
               @if($club->is_Verified)
                   <a href="{{ route('clubs.show', $club->id) }}">
                       <p>{{ $club->name }}</p>
                       <img src="{{ asset($club->profile_picture) }}" alt="{{ $club->name }}">
                   </a>
               @endif
               @endforeach
           @endif
       </div>
     @endforeach


</body>
</html>