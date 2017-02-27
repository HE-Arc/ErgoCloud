/**
 * ErgoView
 * Display heatmap and/or path
 */

class ErgoView {

    constructor(params) {

        this.containerId = params['container'];
        this.isHeatmapEnable = params['heatmap'];
        this.isPathEnable = params['scanpath'];
        this.jsonString = params['json'];

        this.subjects = new Map();


        this.screenshootsDirectory = '';

        this.container = document.getElementById(this.containerId);


        this.ev = this;

        this.heatmapId = this.containerId + '-heatmap';
        this.pathId = this.containerId + '-path';
        this.imageId = this.containerId + '-img';

        this.baseWidth = 1920;
        this.baseHeight = 4096;

        if (this.isPathEnable) {
            this.imgMarginPx = 20;
        }
        else {
            this.imgMarginPx = 0;
        }
        this.scale = this.container.clientWidth / this.baseWidth;

        this.width = this.baseWidth * this.scale;
        this.height = this.baseHeight * this.scale;

        this.minDuration = 400; //min duration in ms for showing point in path

        this.dataPoints = {};
        this.screenshoot = '';


        //Generate placeholders for heatmap, path and image
        this.container.innerHTML =
            '<img id="' + this.imageId + '" src="" width="' + this.width + 'px" style="position: absolute; margin: ' + this.imgMarginPx + 'px "/>\
            <div id="' + this.heatmapId + '" style="position: absolute; top:0; padding: 0; width: 100%; height: 100%;"></div> \
        <canvas id="' + this.pathId + '" width="' + this.width + 'px" height="100%" style="position: absolute; top:0; padding: 0;"></canvas>';

        var heatmapInstance;
        var ev = this.ev;


        this.json = JSON.parse(this.jsonString);

        this.screenshoot = this.json["screenshoot"];
        $('#' + this.imageId).attr('src', this.screenshootsDirectory + this.screenshoot);

        $("#" + this.imageId).load(function () {
            var height = $('#' + this.imageId).height();

            //$("#"+heatmapId + ' > canvas').height(height);
            $('#' + this.containerId).height(height);


            //Create a heatmap instance using heatmap.js library
            this.heatmapInstance = h337.create({
                container: document.getElementById(ev.heatmapId),
                radius: 150 * this.scale,
                maxOpacity: .5
            });

            ev.setData(ev.json);

            if (ev.isPathEnable) {
                ev.addPathButtons();
            }

            ev.redraw();

            $(window).bind('resize', ev.redraw);
        }).bind(ev);
    }


    redraw() {
        this.scale = this.container.clientWidth / this.baseWidth;
        var width = this.baseWidth * this.scale;

        $('#' + this.imageId).width(width);

        var height = $('#' + this.imageId).height();
        $('#' + this.containerId).height(height);


        $("#" + this.heatmapId).width(width);
        $("#" + this.heatmapId).height(height);

        $("#" + this.heatmapId).html('');


        if (this.isHeatmapEnable) {
            //Create a heatmap instance using heatmap.js library
            this.heatmapInstance = h337.create({
                container: document.getElementById(this.heatmapId),
                radius: 150 * this.scale,
                maxOpacity: .5
            });

            this.showHeatmap();

        }

        if (this.isPathEnable) {

            $("#" + this.pathId).attr('width', width + this.imgMarginPx * 2);
            $("#" + this.pathId).attr('height', height + this.imgMarginPx * 2);

            this.showPath();
        }
    }


    setData(data) {
        this.dataPoints = data['points'];

        if (this.isPathEnable) {
            this.populateSubjects();
        }

    }

    loadJson(url) {
        $.getJSON(url, function (json) {
            this.setData(json);
        }, this);
    };

    showHeatmap() {
        var points = [];
        var intensities = [];

        this.isHeatmapEnable = true;

        //Map json eye fixation data, into heatmap.js accepted format
        this.dataPoints.forEach(function (point) {
            var intensity = point['l'];
            intensities.push(intensity);
            points.push({
                x: Math.round(point['x'] * this.scale + this.imgMarginPx),
                y: Math.round(point['y'] * this.scale + this.imgMarginPx),
                value: intensity
            });
        }, this);

        var max = Math.max.apply(Math, intensities);
        var data = {
            max: max,
            data: points
        };


        this.heatmapInstance.setData(data);
    }

    hideHeatmap() {
        this.isHeatmapEnable = false;
        var data = {
            max: 100,
            min: 0,
            data: []
        };
        this.heatmapInstance.setData(data);
    }

    populateSubjects() {
        this.subjects = new Map();
        var colors = ['#16a085', '#f39c12', '#27ae60', '#d35400', '#2980b9', '#c0392b', '#8e44ad', '#bdc3c7', '#2c3e50', '#7f8c8d',
            '#1abc9c', '#f1c40f', '#2ecc71', '#e67e22', '#419fdd', '#e74c3c', '#9b59b6', '#34495e', '#7f8c8d'];

        var colorPtr = 0;


        this.dataPoints.forEach(function (point) {
            var subjectName = point['subject'];

            var subject = this.subjects.get(subjectName);

            //if subject already exist, just add new point
            if (subject) {
                subject['points'].push(point);

            }
            // else : create subject, set color and create empty point list
            else {
                var color = colors[colorPtr];
                colorPtr++;
                var pointList = [];
                var subject = [];
                subject['color'] = color;
                subject['points'] = pointList;
                subject['points'].push(point);
                subject['active'] = true;

                this.subjects.set(subjectName, subject);

            }
        }, this);
    }

    addPathButtons() {

        var html = '';
        var ev = this;

        this.subjects.forEach(function (subject, name, map) {
            html += '<button type="button" originalColor="' + subject.color + '" class="btn btn-primary btn-sm" style="border-color: transparent; background-color: ' + subject.color + ' ">' + name + '</button>\n';
        }, this);

        $('#path-buttons').html(html);

        //toggle subject on button click
        $('#path-buttons button').click(function (event) {
            var target = $(event.target);
            var subject = ev.subjects.get(target.html());

            subject.active = !subject.active;

            if (subject.active) {
                target.css('background-color', target.attr('originalColor'));
            }
            else {
                target.css('background-color', '#95a5a6');
            }

            this.redraw();

        }.bind(this));
    }

    showPath() {

        var canvas = document.getElementById(this.pathId);
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        this.subjects.forEach(function (subject) {

                if (subject.active) {

                    var canvas = document.getElementById(this.pathId);
                    var ctx = canvas.getContext("2d");
                    ctx.fillStyle = "#0000ff";
                    ctx.strokeStyle = subject.color;
                    ctx.lineWidth = 5;
                    ctx.font = "22px Arial";
                    ctx.textAlign = "center";


                    ctx.beginPath();


                    subject['points'].forEach(function (point) {
                        if (point['l'] >= this.minDuration) {

                            ctx.lineTo(point['x'] * this.scale + this.imgMarginPx, point['y'] * this.scale + this.imgMarginPx);

                        }
                    }, this);

                    ctx.stroke();


                    var count = 1;


                    subject['points'].forEach(function (point) {

                        if (point['l'] >= this.minDuration) {
                            ctx.beginPath();
                            ctx.arc(point['x'] * this.scale + this.imgMarginPx, point['y'] * this.scale + this.imgMarginPx, 18, 0, 2 * Math.PI, false);
                            ctx.fillStyle = subject.color;
                            ctx.fill();
                            ctx.lineWidth = 5;
                            ctx.strokeStyle = subject.color;
                            ctx.stroke();

                            ctx.fillStyle = "#ecf0f1";
                            ctx.fillText(count.toString(), point['x'] * this.scale + this.imgMarginPx, point['y'] * this.scale + this.imgMarginPx + 8);

                            count++;
                        }

                    }, this);
                }
            }
            ,
            this
        );
    }

    hidePath() {
        var canvas = document.getElementById(this.pathId);
        var ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }


    /*
     Redraw scanpath with only points from selected minimum duration
     */
    changeMinDuration(duration) {
        this.minDuration = duration;
        this.hidePath();
        this.showPath();
    }

}