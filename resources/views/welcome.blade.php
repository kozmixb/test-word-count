@extends("layouts.app")

@section("content")
    <div class="card my-4">
        <div class="card-header">
            <h2>Programming Test</h2>
        </div>
        <div class="card-body">
            <h5>You can either upload a TXT file or insert an url to an existing TXT file</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Upload File</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{route('filehandling')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="file" id="inputFile1" class="custom-file-input">
                                        <label for="inputFile1" class="custom-file-label">Choose file (.txt | MAX:2MB)</label>
                                    </div>
                                </div>
                                @if($errors->has('file')) 
                                    <div class="alert alert-danger my-1">
                                        {{$errors->first('file')}}
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary my-1">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>URL</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{route('filehandling')}}" method="POST">
                                @csrf
                                <div class="inline-form">
                                    <input type="input" name="url" id="url" class="form-control" placeholder="http://********.txt" value="{{old('url')}}">
                                </div>
                                @if($errors->has('url')) 
                                    <div class="alert alert-danger my-1">
                                        {{$errors->first('url')}}
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary my-2">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <h5>Recently uploaded files</h5>
            <ul class="d-flex flex-wrap">
                @foreach(Storage::files('public/') as $file)
                    @if(substr(basename($file),0,1)!=='.')
                        <form action="{{route('filehandling')}}" method="POST">
                            @csrf
                            <input type="hidden" name="existing" value="{{basename($file)}}">
                            <button class="btn btn-secondary m-1">{{basename($file)}}</button>
                        </form>
                    @endif
                @endforeach
            </ul>
            @if (session('return'))
                <p>{!! session('return') !!}</p>
            @endif
        </div>
    </div>
@endsection