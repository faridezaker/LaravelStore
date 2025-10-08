<h1>لیست برندها</h1>
<ul>
    @foreach($brands as $brand)
        <li>{{ $brand->name }}</li>
    @endforeach
</ul>
