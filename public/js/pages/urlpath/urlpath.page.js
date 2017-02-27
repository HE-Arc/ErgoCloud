/**
 * Main view JS file
 * 
 * TODO : separate code
 */



class Point {
    constructor(x, y) {
        this.x = x;
        this.y = y;
    }

    static distance(a, b) {
        const dx = a.x - b.x;
        const dy = a.y - b.y;

        return Math.sqrt(dx*dx + dy*dy);
    }


    get stringCoords(){
        return String(this.x) + ',' + String(this.y) + ' ';
    }

    get line(){
        return 'L'+this.stringCoords;
    }

    get move(){
        return 'M'+this.stringCoords;
    }
}

var width=5000;
var height=3000;

var pageBoxW=70;
var pageBoxH=55;

var cellW = 1.5*pageBoxW;
var cellH = 1.3*pageBoxH;

var linkStroke = 2;

var pageBoxTitleSize = 14;
var pageBoxSubTitleSize = 12;


var s = Snap('#urlpath');

width=s.node.clientWidth;



class Subject {
    constructor(name, age, occupation, evaluation, color){
        this.name = name;
        this.age = age;
        this.occupation = occupation;
        this.color = color;

        this.evaluation = evaluation;
        if(evaluation == "" || evaluation == null){
            this.evaluation = "Non trouvée";
        }
        

        this.links = [];
    }
}


class Link {
    constructor(subject, srcPage, dstPage){
        this.subject = subject;
        this.srcPage = srcPage;
        this.dstPage = dstPage;
        //Graphic representation
        this.path = null;
    }

    highlightNet(){
        var color = this.color;
        this.subject.links.forEach(function(link, idx){
            link.highlight();
            link.dstPage.box.attr({stroke: color, 'stroke-width': 5});
            if(idx==0){
                link.srcPage.box.attr({stroke: color, 'stroke-width': 5});
            }
        });

    }

    unhighlightNet(){
        this.subject.links.forEach(function(link, idx){
            link.unhighlight();
            link.dstPage.box.attr({stroke: 'grey', 'stroke-width': 0});
            if(idx==0){
                link.srcPage.box.attr({stroke: 'grey', 'stroke-width': 0});
            }
        });
    }

    highlight(){
        this.path.attr({stroke: this.color, strokeWidth: linkStroke*2, fill: 'None'});
    }

    unhighlight(){
        this.path.attr({stroke: this.color, strokeWidth: linkStroke, fill: 'None'});
    }

    get color(){
        return this.subject.color;
    }

    static linkToSubject(link){
        return link.subject;
    }

}


class Page {
    constructor(name){
        this.name = name;
        this.occurrences = [];
    }
}


var gradient = s.gradient('l(0,0,1,0)#ff9c9b-#ffe8be-#bcff6b');

class PageOccurence {

    constructor(page){
        this.page = page;
        this.ins = [];
        this.outs = [];
        this.subjects = [];

        this.subtitle = '';
  

        this.c = 0;
        this.l = 0;

        this.occurrenceNumber = page.occurrences.length+1;
        this.subtitle = String(this.occurrenceNumber);

        this.box=null;

    }

    get center(){
        return new Point(this.c*cellW+0.5*pageBoxW, this.l*cellH+0.5*pageBoxH);
    }

    //Last page of at least one subject, will be later colored in red
    get isLast(){
        //isLast -> if there is not output for at least one subject

        var inSubjects = this.ins.map(Link.linkToSubject);
        var outSubjects = this.outs.map(Link.linkToSubject);

        //if all subjects in inSubjects don't appear in outSubjects --> last = true
        var last = false;
        inSubjects.forEach(function(subject){
            if(jQuery.inArray(subject, outSubjects)==-1){
                last=true;
            }
        });

        return last;
    }

    //First page of at last one subject, will be later colored in green
    get isFirst(){
        //isLast -> if there is not output for at least one subject

        var inSubjects = this.ins.map(Link.linkToSubject);
        var outSubjects = this.outs.map(Link.linkToSubject);

        //if all subjects in outSubjects don't appear in inSubjects --> first = true
        var first = false;
        outSubjects.forEach(function(subject){
            if(jQuery.inArray(subject, inSubjects)==-1){
                first=true;
            }
        });

        return first;
    }

    draw(s){
        var x=this.c*cellW;
        var y=this.l*cellH;

        var pageBox = s.rect(x,y, pageBoxW, pageBoxH,10,10);
        if(this.isFirst && this.isLast){
            pageBox.addClass('page-box-first-last');
            pageBox.attr({fill: gradient});
        }

        else if(this.isFirst)
            pageBox.addClass('page-box-first');
        else if(this.isLast)
            pageBox.addClass('page-box-last');
        else
            pageBox.addClass('page-box');


        var textTitle = s.text(x+pageBoxW/2, y+pageBoxH/2+pageBoxTitleSize/3, this.page.name.substr(-10));
        textTitle.attr({fontSize: pageBoxTitleSize});
        textTitle.addClass('page-box-text');


        var g1 = s.group(pageBox, textTitle);
        var po=this;
        g1.click(function(){pageBox.attr({"stroke-width": 15})});
        this.box = pageBox;


        // Load statistics page on click
        var pageName = this.page.name;

        var redirectFunc = function(){
            window.location = statistic_url + "/" + pageName + "/statistics";
        };


        var showPageDetails = function(){

            $('#modal-title').html(`Page  <b>${pageName}</b>`);
            $('#link-stats').attr('href', `${statistic_url}/${pageName}/statistics`);
            $('#link-heat').attr('href', `${statistic_url}/${pageName}/heatmap`);
            $('#link-scan').attr('href', `${statistic_url}/${pageName}/scanpath`);

            modal.style.display = "block";
        };


        pageBox.mousedown(showPageDetails);
        textTitle.mousedown(showPageDetails);

        var nameHintFunc = function () {
            $('#hint').html('URL Path &nbsp&nbsp&nbsp&nbsp Selected page URL : '  + pageName);
        };

        var nameHintHideFunc = function () {
            $('#hint').html('URL Path');
        };

        pageBox.mouseover(nameHintFunc);
        textTitle.mouseover(nameHintFunc);
        pageBox.mouseout(nameHintHideFunc);

    }


    toString(){
        return this.page.name + '' + this.subtitle;
    }
}


class Board {

    constructor(){
        this.pages = [];
        this.subjects = [];
        this.occurrenceCount = 0;
    }

    //Get an existing page, or create a new one
    getPage(name){

        //search existing
        for(var i=0;i<this.pages.length;i++){
            if(this.pages[i].name == name)
                return this.pages[i];
        }

        //create a new page
        var page = new Page(name);
        this.pages.push(page);
        return page;

    }


    static getNewPageOccurrence(page, subject){
        //if not returned, need to create a new occurrence
        var occurrence = new PageOccurence(page);
        occurrence.subjects.push(subject);
        page.occurrences.push(occurrence);
        return occurrence;
    }


    getPageOccurence(page, subject) {
        //return first occurrence without reference to this subject
        //add this subject to this occurence subjects list


        for(var i=0;i<page.occurrences.length;i++){

            var occurrence = page.occurrences[i];

            if(jQuery.inArray(subject, occurrence.subjects) == -1) {
                //add current subject to occurrence
                occurrence.subjects.push(subject);
                return occurrence;
            }
        }

        //if not returned, need to create a new occurrence
        return Board.getNewPageOccurrence(page, subject);
    }
}

class LoopConflict{
    constructor(subject, pageOccurrence){
        this.subject = subject;
        this.pageOccurrence = pageOccurrence;
    }

    toString(){
        return 'LoopConflict : subject('+ this.subject.color +') pageName('+this.pageOccurrence.page.name + ')';
    }
}

board = new Board();
//subject = new Subject('red');


//var colors = ['red', 'blue', 'green', 'orange', 'grey', 'yellow', ];
//using Flat UI Color palette
var colors = ['#16a085', '#f39c12', '#27ae60', '#d35400', '#2980b9', '#c0392b', '#8e44ad', '#bdc3c7', '#2c3e50', '#7f8c8d',
'#1abc9c', '#f1c40f', '#2ecc71', '#e67e22', '#419fdd', '#e74c3c', '#9b59b6', '#34495e', '#7f8c8d'];

function getColor(id){
    if(id<colors.length)
            return colors[id];
    else
            return "#FF4081";
}


//Add links
var current = null;
var previous = null;
var next = null;





subjectsArray.forEach(function (subjectJson, subjectIndex){

    board.occurrenceCount = 0;

    var subject = new Subject(subjectJson.name, subjectJson.age, subjectJson.occupation, subjectJson.evaluation, getColor(subjectIndex));
    board.subjects.push(subject);
    var trials = subjectJson.trials;
    //if(subjectIndex==1)return;
    //if(subjectIndex==2)return;
    //if(subjectIndex==4)return;
    //if(subjectIndex!=8 && subjectIndex !=9)return;
    //if(subjectIndex>6)return;

    for(var i=0;i<trials.length+1;i++){
        previous = current;
        current = next;

        if(i<trials.length){
            next = board.getPageOccurence(board.getPage(trials[i]), subject);
            //if(next.c == 0)
                //       next.c = board.occurrenceCount;
            board.occurrenceCount++;

            if(next.l == 0)
                next.l = subjectIndex+1;

        }
        else{
            next = null;
        }

        if(current != null){
            if(previous != null){
                var input = new Link(subject, previous, current);
                current.ins.push(input);
                previous.outs.push(input);
            }
        }
    }
});


var col = 0;
var lin = 0;
var cells = [];



board.pages.forEach(function (page) {
    page.occurrences.forEach(function (occurrence) {
        //occurrence.c = col;
        //occurrence.l = Math.floor(Math.random()*10);

        cells.push(occurrence);
        col++;
        lin++;
    });
});


//Determine FIRST
var first = null;
cells.forEach(function(cell){
    if(cell.ins.length==0)
            first = cell;
});

console.log(first);


var itcount = 0;

function searchLoopConflictRight(current, searched, level){
    //console.log(level+1);
    itcount++;
    if(itcount>10000000)
        return "itcount";
    var conflict = null;
    //console.log("Check " + String(current) + " outputs");
    //current.outs.forEach(function (output) {

    for(var i=0;i<current.outs.length && conflict==null ;i++){
        var output = current.outs[i];
        var dstPage = output.dstPage;
        if(jQuery.inArray(dstPage, searched)!=-1)
            conflict = new LoopConflict(output.subject, dstPage);

        else {
            var newSearched = searched.slice();
            newSearched.push(dstPage);
            //console.log("Used : " + String(newSearched));
            conflict = searchLoopConflictRight(dstPage, newSearched, level+1);
        }
    }
    return conflict;
}


//Migrate links corresponding to subject from 'from' list to 'to' list
function migrateLinks(from, to, subject){
    itcount=0;
    var done=false;

    //Inputs
    do{
        itcount++;
        if(itcount>1000)
            return "itcount";
        var i=0;


        var originLenght = from.ins.length;
        for(;i<originLenght;i++){
            var link = from.ins[i];
            if(link.subject == subject){
                //Add link to new occurrence
                to.ins.push(link);
                //Remove link from origin occurrence
                from.ins.splice(i,1);

                //Change link destination
                link.dstPage=to;

                //Splice change list indexes and could cause problems
                // dirty solution: restart 'for loop' to do it again with new indexes
                break;
            }
        }
        if(i==originLenght)
                done=true;

    }while(!done);

    done=false;
    //Outputs
    do{
        itcount++;
        if(itcount>1000)
            return "itcount";
        var i=0;


        var originLenght = from.outs.length;
        for(;i<originLenght;i++){
            var link = from.outs[i];
            if(link.subject == subject){
                //Add link to new occurrence
                to.outs.push(link);
                //Remove link from origin occurrence
                from.outs.splice(i,1);

                //Change link source
                link.srcPage=to;

                //Splice change list indexes and could cause problems
                // dirty solution: restart 'for loop' to do it again with new indexes
                break;
            }
        }
        if(i==originLenght)
            done=true;

    }while(!done);
}

//Page Occurrence creation and links migrations
function splitOccurrence(subject, pageOccurence, occurrences){
    //Create an empty occurrence
    var newOccurrence = Board.getNewPageOccurrence(pageOccurence.page, subject);
    newOccurrence.l = pageOccurence.l+Math.floor(Math.random()*5);

    //Migrate links
    migrateLinks(pageOccurence, newOccurrence, subject);

    occurrences.push(newOccurrence);

}


//Loop remover
//Remove link loop in pageOccurences, creating new occurrences if necessary

do{
    var cell = cells[0];

    console.log("loop");
    var conflict = searchLoopConflictRight(cell, [cell], 0);

    if(conflict!=null){
        if(conflict == "itcount"){
            $('.panel-heading').html('itcount');
            break;
        }
        console.log("Itcount " + itcount);
        splitOccurrence(conflict.subject, conflict.pageOccurrence, cells);
        console.log('%c' + String(conflict) + " " + conflict.pageOccurrence, 'color: orange');
    }

}while(conflict!=null);


//Multipass algorithm for positioning cells horizontally
//New column is max(column of all ins) + 1

for(var pass=0;pass<25;pass++){
    cells.forEach(function(cell){
        var maxPreviousC=-1;
        cell.ins.forEach(function(link){
            if(link.srcPage.c > maxPreviousC){
                maxPreviousC=link.srcPage.c;
            }
        });
        cell.c = maxPreviousC + 1;
    });
}


function getCellAtCoords(l,c,cells){
    for(var i=0;i<cells.length;i++){
        var cell = cells[i];
        if(cell.c == c && cell.l == l)
            return cell;
    }
    return null;
}


//Cell stack suppression
cells.forEach(function(thisCell){
    cells.forEach(function(otherCell){
        if(thisCell.c == otherCell.c && thisCell.l == otherCell.l && thisCell!=otherCell){
            console.log("Overlap");
            console.log(thisCell);
            while(getCellAtCoords(otherCell.l++, otherCell.c, cells));
        }
    });
});


//Move cells to avoid route conflicts
for(var i=0;i<5;i++) {
    cells.forEach(function (cell) {

        cell.outs.forEach(function (link) {
            //Is link horizontal ?
            //if(cell.l == link.dstPage.l){
            //Not in next collumn =
            //if(link.dstPage.c != cell.c+1){
            for (var cIntra = cell.c + 1; cIntra < link.dstPage.c; cIntra++) {
                cellIntra = getCellAtCoords(cell.l, cIntra, cells);
                var cellCheck=cellIntra;
                while(cellCheck){
                    cellCheck = getCellAtCoords(cellIntra.l+1, cIntra, cells);
                    cellIntra.l++;
                }
                cellIntra = getCellAtCoords(link.dstPage.l, cIntra, cells);
                while(cellCheck){
                    cellCheck = getCellAtCoords(cellIntra.l+1, cIntra, cells);
                    cellIntra.l++;
                }

            }
        });
    });
}


//Merge occurrences of same page when in the same column
merges = [];

//Mark for merge
board.pages.forEach(function(page){
    for(var i=0;i<page.occurrences.length;i++){
        for(var j=i+1;j<page.occurrences.length;j++){
            if(page.occurrences[i].c == page.occurrences[j].c){
                merges.push([page.occurrences[i], page.occurrences[j]])
            }
        }
}
});

console.log(merges);

merges.reverse();
merges.forEach(function(merge){
    // second occurrence will be deleted
    keepOccurrence = merge[0];
    deleteOccurrence = merge[1];

    //Modify link in delete occurrence to match with keep occurrence
    deleteOccurrence.ins.forEach(function(link){
        link.dstPage=keepOccurrence;
    });

    deleteOccurrence.outs.forEach(function(link){
        link.srcPage=keepOccurrence;
    });

    // - Copy links to keep occurrence
    Array.prototype.push.apply(keepOccurrence.ins, deleteOccurrence.ins);
    Array.prototype.push.apply(keepOccurrence.outs, deleteOccurrence.outs);

    // - deleteOccurrence
    deleteOccurrence.page.occurrences = deleteOccurrence.page.occurrences.filter(occurrence => occurrence != deleteOccurrence);
    cells = cells.filter(cell => cell != deleteOccurrence);

});


//Vertically align cells with 1:1 link relation
for(var i=0;i<3;i++) {
    cells.forEach(function (cell) {
        if (cell.outs.length > 0) {
            //Check all outs go to same dst page
            firstDst = cell.outs[0].dstPage;
            if (cell.outs.every(link => link.dstPage == firstDst))
            {
                //Check all ins of this page come from same source
                if (firstDst.ins.every(link => link.srcPage == cell))
                {
                    console.log(firstDst);
                    //Check emplacement availability (we move first dst y to cell y)
                    if (!getCellAtCoords(firstDst.c, cell.l, cells)) {
                        //Apply move
                        console.log('Move ' + String(firstDst));
                        firstDst.l = cell.l;

                    }
                }
            }
        }
    });
}

//Add links references to subjects (Used for net highlighting)
board.subjects.forEach(function(subject){
    board.pages.forEach(function(page){
        page.occurrences.forEach(function(occurrence){
            occurrence.outs.forEach(function(link){
            if(link.subject == subject){
                subject.links.push(link);
            }
            });
        });
    });
});

//Route links
cells.forEach(function(cell){
    cell.outs.forEach(function(link){
        route(link);
    });
});


cells.forEach(function(cell){
    cell.draw(s);
});



function route(link){
    //routeDirect(link);
    route90(link);

}

//LinkLock : When true, prevent link to be highlighted when mouse passes over.
linkLock = false;

//Reference to locked link, when applicable.
lockedLink = null;

function route90(link){
    var start = link.srcPage.center;
    var startOutCount = link.srcPage.outs.length;
    var srcPos = link.srcPage.outs.findIndex(function(lk){return lk==link});
    start.y = start.y + linkStroke*1.5 * (srcPos - Math.floor(startOutCount/2));
    var stop = link.dstPage.center;
    var stopInCount = link.dstPage.ins.length;
    var destPos = link.dstPage.ins.findIndex(function(lk){return lk==link});
    stop.y = stop.y + linkStroke*1.5 * (destPos - Math.floor(stopInCount/2));
    var sign = +1;
    if(stop.y<start.y)
            sign = -1;
    var vertex1 = new Point(start.x + pageBoxW/2 + (cellW-pageBoxW)/2 - linkStroke*1.5 * (srcPos -Math.floor(startOutCount/2))*sign, start.y);
    var vertex2 = new Point(vertex1.x, stop.y);

        //console.log(start.move + vertex1.line + vertex2.line + stop.line);
    var path = s.path(start.move + vertex1.line + vertex2.line + stop.line);
    path.attr({stroke: link.color, strokeWidth: linkStroke, fill: 'None'});

    link.path = path;

    //
    var over = function(){
        if(!linkLock) {
            link.highlightNet();
        }
    };

    var out = function () {
        if(!linkLock) {
            link.unhighlightNet();
        }
    };

    var showLinkInfo = function(link){
        $('#linkDetails > .subject').html(link.subject.name + '(' + link.subject.age + ' / ' + link.subject.occupation + ')' + '<br/> <span class="eval-subject"> Evaluation: <span class="eval-result label label-success">'+ link.subject.evaluation +'</span></span>');
        $('#linkDetails > .color').css("background-color", link.subject.color);
        $('#linkDetails').show();
    };

    var hideLinkInfo = function(){
        $('#linkDetails').hide();
    };

    var click = function () {
        //Toogle link lock
        linkLock = !linkLock;
        // highlight : this is not a duplicate for over
        // -- in case of triple click (lock-unlock-lock), we don't have a second mouse-over event
        link.highlightNet();
        showLinkInfo(link);

        //Lock
        if(linkLock){
            lockedLink = link;
        }

        //Unlock : unhighlight and check if an other link is clicked
        else{
            if(lockedLink != null){
                lockedLink.unhighlightNet();

                //Other link clicked
                if(link!=lockedLink){
                    //Switch to new link
                    lockedLink=link;
                    linkLock=true;
                    link.highlight();
                    showLinkInfo(link);

                }
                else{
                    linkLock=false;
                    hideLinkInfo();
                }
            }
        }
    };


    //Mouse interaction for links
    path.mouseover(over);
    path.mouseout(out);
    path.click(click);

}

function routeDirect(link){
    centerA = link.srcPage.center;
    centerB = link.dstPage.center;
    line = s.line(centerA.x, centerA.y, centerB.x, centerB.y);
    line.attr({stroke: link.color, strokeWidth: 2});
}


function grid(){
    for(i=0;i<30;i++){
        hLine = s.line(0, cellH * i, width-1, cellH * i);
        hLine.addClass('grid');
        t = s.text(3, cellH*(i+0.5), String(i));
        t.addClass('grid');
        vLine = s.line(cellW * i, 0, cellW * i, height-1);
        vLine.addClass('grid');
        t = s.text(cellW*(i+0.5), 13, String(i));
        t.addClass('grid');
    }
}

function gridRoute() {
    for(i=0;i<100;i++){
        hLine = s.line(0, cellH * i/10, width-1, cellH * i/10);
        hLine.addClass('grid-route');

        vLine = s.line(cellW * i/10, 0, cellW * i/10, height-1);
        vLine.addClass('grid-route');

    }
}

//grid();

var panZoom = svgPanZoom('#urlpath');