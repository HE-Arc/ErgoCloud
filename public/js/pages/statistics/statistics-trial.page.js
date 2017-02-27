/**
 * Main view JS file
 */


/**
 * Entry point
 * */
$( document ).ready(function() {
   
   var nbSubjects = parseInt($('#subject-count').html());
   generationStatsChart(nbSubjects);
   sexChart(nbSubjects);
   langageChart(nbSubjects);
   glassesChart(nbSubjects);
   handednessChart(nbSubjects);
   typeChart(nbSubjects);

});