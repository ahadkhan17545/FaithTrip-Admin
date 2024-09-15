@php
    $sabreGdsInfo = DB::table('sabre_gds_configs')
                        ->leftJoin('gds', 'sabre_gds_configs.gds_id', 'gds.id')
                        ->select('sabre_gds_configs.*', 'gds.status')
                        ->where('sabre_gds_configs.id', 1)
                        ->first();

    if($sabreGdsInfo->status == 1 && $sabreGdsInfo->is_production == 0){
        echo "<span style='color: red; font-weight: 600; border: 2px solid red; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Sabre Sandbox</span>";
    }
    if($sabreGdsInfo->status == 1 && $sabreGdsInfo->is_production == 1){
        echo "<span style='color: green; font-weight: 600; border: 2px solid green; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Sabre Live</span>";
    }
@endphp

@php
    $flyhubGdsInfo = DB::table('flyhub_gds_configs')
                        ->leftJoin('gds', 'flyhub_gds_configs.gds_id', 'gds.id')
                        ->select('flyhub_gds_configs.*', 'gds.status')
                        ->where('flyhub_gds_configs.id', 1)
                        ->first();

    if($flyhubGdsInfo->status == 1 && $flyhubGdsInfo->is_production == 0){
        echo "<span style='color: red; font-weight: 600; border: 2px solid red; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Flyhub Sandbox</span>";
    }
    if($flyhubGdsInfo->status == 1 && $flyhubGdsInfo->is_production == 1){
        echo "<span style='color: green; font-weight: 600; border: 2px solid green; padding: 2px 10px; border-radius: 4px; margin-right: 10px; font-size: 13px;'><i class='fa fa-circle' style='font-size: 10px;'></i> Flyhub Live</span>";
    }
@endphp
