<div class="panel panel-primary" id="filterTest">
    <div class="panel-heading panel-heading-controls">
        <h1>Filtres sur le test</h1>
        <a class="btn btn-danger pull-right disable">Désactiver</a>
    </div>

    <div class="loading-box" id="loading-box-tests">
        <p>
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br/>
        </p>
    </div>

    <div class="panel-body" id="testFilters">                    

        <table class="table table-striped table-filters">
            <tr>
                <th>Moyennes</th>
                <th colspan="4">Filtres</th>
            </tr>
            
            <tr>
                <td class="avr-column"><span id="avr_time">0</span></td>
                <td>Durée du test</td>

                <td><input type="text" class="range-input" id="range-input-duration"></td>
                <td>
                    <input class="slider" id="duration" data-slider-id='durationSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)" />                               
                </td>
                
                <td>
                    <div>
                        <input type="checkbox" id="lessduration" style="margin-left: 3%;" onchange="addToFilters(this.id, 'testFilters')">
                        <span id="min_duration" >0 </span>
                        <strong > à </strong>   
                        <span class="range_duration" style=" color: #269abc">0 </span>
                    </div>
                    
                    <div>
                        <input type="checkbox" id="moreduration" style="margin-left: 3%" onchange="addToFilters(this.id, 'testFilters')"> 
                        <span class="range_duration" style=" color: #269abc">0 </span>
                        <strong> à </strong>                   
                        <span id="max_duration">0</span>
                    </div>
                </td>
            </tr>


            <tr>
                <td class="avr-column"><span id="avr_visited">0</span></td>
                <td>Nombre de pages visitées</td>

                <td><input type="text" class="range-input" id="range-input-visited"></td>

                <td>
                    <input class="slider" id="visited" data-slider-id='visitedSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)" />
                </td>
                
                <td>
                    <div>
                        <input type="checkbox" id="lessvisited" style="margin-left: 3%;" onchange="addToFilters(this.id, 'testFilters')">
                        <span id="min_visited" >0 </span> <strong>à</strong> 
                        <span class="range_visited" style=" color: #269abc">0 </span>   
                    </div>
                    
                    <div>
                        <input type="checkbox" id="morevisited" style="margin-left: 3%" onchange="addToFilters(this.id, 'testFilters')">
                        <span class="range_visited" style=" color: #269abc">0 </span>
                        <strong>à</strong>    
                        <span id="max_visited">0 </span>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="avr-column"><span id="avr_perpage">0</span></td>
                <td>Durée moyenne par page</td>

                <td><input type="text" class="range-input" id="range-input-perpage"></td>
                <td>
                        <input class="slider" id="perpage" data-slider-id='perpageSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                </td>
                
                <td>
                        <div>
                        <input type="checkbox" id="lessperpage" style="margin-left: 3%;" onchange="addToFilters(this.id, 'testFilters')">
                        <span id="min_perpage" >0 </span> <strong >à</strong> 
                        <span class="range_perpage" style=" color: #269abc">0 </span>     
                    </div>
                    
                    <div>
                        <input type="checkbox" id="moreperpage" style="margin-left: 3%" onchange="addToFilters(this.id, 'testFilters')">
                        <span class="range_perpage" style=" color: #269abc">0 </span> <strong>à</strong>  
                        <span id="max_perpage">0 </span>
                    </div>
                </td>
            </tr>
            
        </table>



            
        <input type="checkbox" id="abandon" > Abandon <span id="abandonText"></span><hr class="common">
        <span>Chemin optimal</span>

        <a class="btn btn-info dropdown-toggle" id="pathToggle" data-toggle="dropdown" data-id=""  href="#">
            <span class="caret"></span>
        </a> <hr class="common">
      
        <br>
        <div class="col-lg-12" id="path" > 
            <div class="row">
                <div class="col-lg-5">   
                    <strong>Pages</strong>
                    <select id="trials" multiple="multiple" size =20 style="width: 100%" >
                            <option value="one" style="color: #269abc">One slide</option>
                            <option value="more" style="color: #269abc">More slides</option>
                            @foreach($trials as $trial)                         
                                <option value="{{$trial->id}}">{{$trial->name}}</option>
                            @endforeach                                
                    </select>
                </div>
                <div class="col-lg-5" id="pagePath" >
                    <strong>Chemin optimal</strong>
                    <select id="chosentrials" multiple="multiple" size =20 style="width: 100%">                                    
                    </select>
                </div>
            </div>
            <a class="btn btn-success btn-block" id="createPath">Créer le chemin</a>
            

            <div class="row">
                <table id="pathtable">     
                </table>
            </div>
        </div>                        
    </div>  
</div>