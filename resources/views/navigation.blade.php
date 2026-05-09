<x-top-nav></x-top-nav>

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

   <!-- 🔍 Search Bar -->
   <div class="search-bar">
       <form action="{{ route('clubs.search') }}" method="GET">
           <input type="text" name="query" placeholder="Search clubs or events..."
                  value="{{ request('query') }}">
           <button type="submit">Search</button>
       </form>
   </div>

   <!-- ✅ Dynamic Categories Loop -->
   @foreach (\App\Enums\ClubCategory::cases() as $category)
       <h2>{{ $category->value }}</h2>
       <div class="container">
           @foreach ($clubs->where('category', $category->value) as $club)
               <a href="{{ route('clubs.show', $club->id) }}">
                   <p>{{ $club->name }}</p>
                   <img src="{{ asset($club->profile_picture) }}" alt="{{ $club->name }}">
               </a>
           @endforeach
       </div>
   @endforeach
</body>
</html>