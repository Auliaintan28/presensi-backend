@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Rekap Presensi</div>
                
                <div class="card-body">
                <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Lokasi (Masuk & Pulang)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensis as $item)
                            <tr>
                                <td>{{$item->name}}</td>
                                <td>{{$item->tanggal}}</td>
                                
                                <!-- Kolom Masuk (dengan Status) -->
                                <td>
                                    {{$item->masuk}}
                                    @if($item->is_terlambat)
                                        <span class="badge badge-danger">Terlambat</span>
                                    @endif
                                </td>
                                
                                <!-- Kolom Pulang (dengan Status) -->
                                <td>
                                    {{$item->pulang ?? '-'}}
                                    @if($item->is_pulang_cepat)
                                        <span class="badge badge-warning">Pulang Cepat</span>
                                    @endif
                                </td>
                                
                                <!-- Kolom Lokasi (Link ke Google Maps) -->
                                <td>
                                    <!-- Cek apakah lokasi datang ada -->
                                    @if($item->latitude_datang)
                                        <a href="https://www.google.com/maps?q={{$item->latitude_datang}},{{$item->longitude_datang}}" target="_blank">
                                            Lokasi Masuk
                                        </a>
                                    @else
                                        -
                                    @endif
                                    
                                    <br> <!-- Beri baris baru -->
                                    
                                    <!-- Cek apakah lokasi pulang ada -->
                                    @if($item->latitude_pulang)
                                        <a href="https://www.google.com/maps?q={{$item->latitude_pulang}},{{$item->longitude_pulang}}" target="_blank">
                                            Lokasi Pulang
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                </table>
                </div>
            </div>
        </div>
        
        
    </div>
</div>
<script type="text/javascript" class="init">
    

$(document).ready(function () {
    var table = $('#example').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true
    } );
});

</script>

@endsection