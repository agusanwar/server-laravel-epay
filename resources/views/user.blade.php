@extends('base')

@section('title', 'User')

@section('header_title', 'User')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <div class="card-body table-responsive p-0">
            <table id="user" class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Username</th>
                  <th>Verified</th>
                  <th>Profile Picture</th>
                  <th>Identitas</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($users as $user)
                  <tr> 
                      <td>{{ $user->id }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->email  }}</td>
                      <td>{{ $user->username  }}</td>
                      <td>{{ $user->verified }}</td>
                      <td>{{ $user->profile_picture}}</td>
                      <td>{{ $user->identitas }}</td>
                      <td>{{ $user->created_at  }}</td>
                  </tr>
                @endforeach 
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

 {{-- data table --}}
@section('js')
  <script>
    $('#transactions').dataTable();
  </script>
@endsection