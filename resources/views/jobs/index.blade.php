<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs</title>
</head>
<body>
<h1>Estado de los cruces</h1>

@if (count($completedJobs))
    <h2>Cruces completas</h2>
<ul>
    {{count($completedJobs)}}
</ul>
@endif


@if (count($inProcessJobs))
    <h2>
        Trabajando en {{count($inProcessJobs)}} colas
    </h2>
@else
    <p>Sin pendientes</p>
@endif
</body>

</html>
