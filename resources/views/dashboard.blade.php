@extends('layouts.master')

@section('title', '- Inicio')

@section('content')

<style>

.dashboard{
    display: flex;
    overflow: scroll;
    width: 100%;
    padding-top: 1ex;
}
    
    .dash_element{
        width: 300px;
        min-width: 300px;
        min-height: 500px;
        margin-right: 1em;
        border: solid 1px #DBDBDB;
        border-radius: 1ex;
    }
    .dash_element .dash_title{
        background-color: #E7F2F6;
        line-height: 3em;
    font-weight: bold;
    padding:0 1em;
    color: #1283AB;
    font-size: 1.1em;
    text-transform: capitalize;
    }
    .hud{
        display: flex;
        width: 300px;
        padding: 1em 0;
    }
    .hud svg{
        max-width: 100%;
        width: 100%;
        height: 120px;
    }
    .emotions{
        display: flex;
        align-items: center;
        justify-content: space-around;
    }
    .emotions img.most_emotion {
        width: 5.5em;
        height: 6em;
    }
    .emotions .percentage{
        font-size: 2.5em;
        padding-right: 0.5ex;
    }
    .sub{
    height: 150px;
    padding: 1em;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    }
</style>


<script>
var companies_objectives = [];
var angles = [-60,0,70,140,140].reverse()
function getEmotionsDepartmentSummary(department_id){

    $.get('/get_emotion_summary_department/'+department_id, function(){},'json')
    .done(function(d){
        var rawactive = d.active
        var emotions = {}
        emotions.counts = {};
        emotions.names = {};
        for (var i = 0; i < rawactive.length; i++) {
            emotions.names[rawactive[i].active_emotion_id] = rawactive[i].name;
            emotions.counts[rawactive[i].active_emotion_id] = 0;
            emotions.total = 0;
        };
        var all_emotions = d.data;

        all_emotions.forEach(function(d){
            this.counts[d.emotion]++;
            this.total++;
        },emotions)

        var ids = Object.keys(emotions.counts);
        var current = 0; var maximum = 0;

        for (var i = 0; i < ids.length; i++) {
            if (emotions.counts[ids[i]] > maximum){
                current = ids[i];
                maximum = emotions.counts[ids[i]];
            }
        };

        var final_emotion = emotions.names[current];

        var percentage = ((1 / (emotions.total / maximum))  * 100).toFixed(2)

        $('#depto_'+department_id+' .most_emotion').attr('src', '/img/emociones/'+final_emotion+'.svg' );
        $('#depto_'+department_id+' .most_emotion').attr('title', final_emotion);
        $('#depto_'+department_id+' .emotions').append('<span class="percentage"> '+percentage+'% </span' );
    });
}

function getEmotionsCompanySummary(){

    $.get('/get_emotion_summary_company/', function(){},'json')
    .done(function(d){
        var rawactive = d.active
        var emotions = {}
        emotions.counts = {};
        emotions.names = {};
        for (var i = 0; i < rawactive.length; i++) {
            emotions.names[rawactive[i].active_emotion_id] = rawactive[i].name;
            emotions.counts[rawactive[i].active_emotion_id] = 0;
            emotions.total = 0;
        };
        var all_emotions = d.data;

        all_emotions.forEach(function(d){
            this.counts[d.emotion]++;
            this.total++;
        },emotions)

        var ids = Object.keys(emotions.counts);
        var current = 0; var maximum = 0;

        for (var i = 0; i < ids.length; i++) {
            if (emotions.counts[ids[i]] > maximum){
                current = ids[i];
                maximum = emotions.counts[ids[i]];
            }
        };

        var final_emotion = emotions.names[current];

        var percentage = ((1 / (emotions.total / maximum))  * 100).toFixed(2)

        $('#company .most_emotion').attr('src', '/img/emociones/'+final_emotion+'.svg' );
        $('#company .most_emotion').attr('title', final_emotion);
        $('#company .emotions').append('<span class="percentage"> '+percentage+'% </span' );
    });
}
function getObjectivesDepartmentSummary(department_id){

    $.get('/get_objective_summary_department/'+department_id, function(){},'json')
    .done(function(d){
        var objectives = d.data;

        objectives.forEach(function(d){
            var cumulative = parseFloat(d.real)/d.days
            if (cumulative > d.daily_yellow_ceil) { d.semaf = 3; return;}
            if (cumulative > d.daily_yellow_floor) { d.semaf = 2; return;}
            d.semaf = 1;
            
        });
        var sem_dept = objectives.map(function(d){
            return d.semaf
        }).reduce(function(p,c){return p + c}) / objectives.length ;
        if (sem_dept == 3) {
            sem_dept = 4;
        }
        console.log(sem_dept);
        var rotateto = 'rotate( '+angles[Math.ceil(sem_dept)]+' 200 217)';
        $('#depto_'+department_id+' .objectives #Layer_4').attr('transform', rotateto )
        
    });
}

function getObjectivesCompanySummary(){

    $.get('/get_objective_summary_company/', function(){},'json')
    .done(function(d){
        var objectives = d.data;

        objectives.forEach(function(d){
            var cumulative = parseFloat(d.real)/d.days
            if (cumulative > d.daily_yellow_ceil) { d.semaf = 3; return;}
            if (cumulative > d.daily_yellow_floor) { d.semaf = 2; return;}
            d.semaf = 1;
            
        });
        var sem_dept = objectives.map(function(d){
            return d.semaf
        }).reduce(function(p,c){return p + c}) / objectives.length ;
        if (sem_dept == 3) {
            sem_dept = 4;
        }
        console.log(sem_dept);
        var rotateto = 'rotate( '+angles[Math.ceil(sem_dept)]+' 200 217)';
        $('#company .objectives #Layer_4').attr('transform', rotateto )
        
    });
}
function getPrioritiesDepartmentSummary(department_id){

    $.get('/get_priority_summary_department/'+department_id, function(){},'json')
    .done(function(d){
        var priorities = d.data;

        var sem_priorities = [];
        priorities.forEach(function(d){
            var weeks = [d.w1, d.w2, d.w3, d.w4, d.w5, d.w6, d.w7, d.w8, d.w9, d.w10, d.w11, d.w12, d.w13];

            var sem_priority = weeks.reduce(function(p,c){ return parseFloat(p)+parseFloat(c)}) / weeks.length;

            sem_priorities.push(sem_priority);
        });

        var sem_priority = sem_priorities.reduce(function(p,c){ return parseFloat(p)+parseFloat(c)}) / sem_priorities.length;
        var rotateto = 'rotate( '+angles[Math.ceil(sem_priority)]+' 200 217)';
        
        $('#depto_'+department_id+' .priorities #Layer_4').attr('transform', rotateto )
        
    });
}
function getPrioritiesCompanySummary(){

    $.get('/get_priority_summary_company/', function(){},'json')
    .done(function(d){
        var priorities = d.data;

        var sem_priorities = [];
        priorities.forEach(function(d){
            var weeks = [d.w1, d.w2, d.w3, d.w4, d.w5, d.w6, d.w7, d.w8, d.w9, d.w10, d.w11, d.w12, d.w13];

            var sem_priority = weeks.reduce(function(p,c){ return parseFloat(p)+parseFloat(c)}) / weeks.length;

            sem_priorities.push(sem_priority);
        });

        var sem_priority = sem_priorities.reduce(function(p,c){ return parseFloat(p)+parseFloat(c)}) / sem_priorities.length;
        var rotateto = 'rotate( '+angles[Math.ceil(sem_priority)]+' 200 217)';
        
        $('#company .priorities #Layer_4').attr('transform', rotateto )
        
    });
}

</script>

<div class="dashboard">
<!-- ITEM -->

<div class="dash_element" id="company">
    <div class="dash_section">
        <div class="dash_title">
            {{ session('company_name') }}
        </div>
        <div class="sub"  style="background-image:url({{ Session::get('company_logo')}})">
            
        </div>
    </div>
    <div class="dash_section">
        <div class="dash_title">
            {{trans_choice('general.menu.objectives',2)}}
        </div>
        <div class="hud objectives">
            <?php include base_path('resources/svg/hud2.svg'); ?>
        </div>
    </div>
    <div class="dash_section">
        <div class="dash_title">
            {{trans_choice('general.menu.priorities',2)}}
        </div>
        <div class="hud priorities">
            <?php include base_path('resources/svg/hud2.svg'); ?>
        </div>
    </div>
    <div class="dash_section">
        <div class="dash_title">
            {{trans_choice('general.menu.emotions',2)}}
        </div>
        <div class="emotions">
            <img title="" class="most_emotion" src="/img/emociones/none.svg" alt="">
        </div>
    </div>
</div>

<script>
    getObjectivesCompanySummary();
    getPrioritiesCompanySummary();
    getEmotionsCompanySummary();
</script>

<!-- ITEM -->

    @foreach ($departments as $department)
        @include('dashboard_item', array('department' => $department)) 
    @endforeach

    <div>
        
    <h2>{{trans_choice('general.mine',2)}} {{trans_choice('general.menu.objectives',2)}}</h2>
        <div class="objectives_charts">
        </div>
    </div>
</div>
<div class="my_summary">
    @include('my_summary', array('objectives' => Auth::user()->objectives , 'priorities' => Auth::user()->priorities , 'dreams' => $dreams )) 
</div>

@stop