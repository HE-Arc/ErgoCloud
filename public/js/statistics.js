$( document ).ready(function() {
   
   var nbSubjects = parseInt($('#subject-count').html());
   generationStatsChart(nbSubjects);
   sexChart(nbSubjects);
   langageChart(nbSubjects);
   glassesChart(nbSubjects);
   ambianceChart(nbSubjects);
   handednessChart(nbSubjects);
   typeChart(nbSubjects);
   eliminateChart(nbSubjects);
   lightChart(nbSubjects);
   ambianceChart(nbSubjects);
});


function typeChart(nbSubjects){
    showPieChart(nbSubjects, '.type-row', '.type-row-name', '.type-row-count', 'type-chart');
}

function eliminateChart(nbSubjects){
    showPieChart(nbSubjects, '.eliminate-row', '.eliminate-row-name', '.eliminate-row-count', 'eliminate-chart');
}

function lightChart(nbSubjects){
    showPieChart(nbSubjects, '.light-row', '.light-row-name', '.light-row-count', 'light-chart');
}

function ambianceChart(nbSubjects){
    showPieChart(nbSubjects, '.ambiance-row', '.ambiance-row-name', '.ambiance-row-count', 'ambiance-chart');
}

function handednessChart(nbSubjects){
    showPieChart(nbSubjects, '.handedness-row', '.handedness-row-name', '.handedness-row-count', 'handedness-chart');
}

function glassesChart(nbSubjects){
    showPieChart(nbSubjects, '.glasses-row', '.glasses-row-name', '.glasses-row-count', 'glasses-chart');
}

function langageChart(nbSubjects){
    showPieChart(nbSubjects, '.langages-row', '.langages-row-name', '.langages-row-count', 'langages-chart');
}

function sexChart(nbSubjects){
    showPieChart(nbSubjects, '.sex-row', '.sex-row-name', '.sex-row-count', 'sex-chart');
}

function generationStatsChart(nbSubjects){
    showPieChart(nbSubjects, '.generation-row', '.generation-row-name', '.generation-row-count', 'generations-chart');
}

/**
 * Show PiChart
 * subjectCount: number => nb subjects
 * elementRowClassParent: string => class name of the parent row
 * elementRowClassName: string => class name of the row with the name
 * elementRowClassCount: string => class name of the row with nb
 * elementChartId: string => id of element to display
 */
function showPieChart(subjectCount, elementRowClassParent, elementRowClassName, elementRowClassCount, elementChartId){
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var datas = [['Data', 'Count percent']];

        $(elementRowClassParent).each(function( index ) {
            var name = $(this).find(elementRowClassName).html();
            var value = parseInt($(this).find(elementRowClassCount).html());
            var percent = value / subjectCount * 100;

            datas.push([name, percent]);
        });

        var data = google.visualization.arrayToDataTable(datas);

        var options = {
            chartArea:{left:10,top:10,width:"100%",height:"90%"},
            backgroundColor: { fill:'transparent' }
        };

        var chart = new google.visualization.PieChart(document.getElementById(elementChartId));

        chart.draw(data, options);
    }
}

