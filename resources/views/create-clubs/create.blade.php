<x-top-nav>
</x-top-nav>

<!DOCTYPE html>
<html>
<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Clubd</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/create-clubs.css') }}">
</head>

<header>
    <img src="{{ asset('images/csrw-placeholder-2.jpeg') }}" 
         style="width:100%; height:200px; object-fit:cover;">
    <h1>Want to Introduce a new club to MMU?</h1>
   </header>


<body>
<div >
    <h2 id = "create-club-h2">Create Club</h2>
    <p>Fill in the details below</p>

<div class="create-club-form">
    <form action="{{ route('create-clubs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group-clubs">
            <label for="name">Name</label><br>
            <input type="text" name="name" id="name">
        </div>

        <div class="form-group-clubs">
            <label for="description">Description</label><br>
            <input name="description" id="description"></input>
        </div>

        <div class="form-group-clubs">
            <label for="profile_picture">Profile Picture</label><br>
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <div class="form-group-clubs">
            <label for="category">Category</label><br>
            <select name="category" id="category">
                <option value="Arts Clubs">Art Clubs</option>
                <option value="Community Clubs">Community Clubs</option>
                <option value="Religious Clubs">Religious Clubs</option>
                <option value="Games / Entertainment Clubs">Games / Entertainment Clubs</option>
                <option value="Cultural Clubs">Cultural Clubs</option>
                <option value="Tech Clubs">Tech Clubs</option>
                <option value="Recreational / Physical Activities Clubs">Recreational / Physical Activities Clubs</option>
            </select>
        </div>

        <button type="submit" class="btn-submit-clubs">Create Club</button>
    </form>

</div>
</div>
</body>
</html>





