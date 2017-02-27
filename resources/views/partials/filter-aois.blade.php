 <div class="panel panel-primary" id="filterTest">
    <div class="panel-heading panel-heading-controls">
        <h1>Filtres sur AOI</h1>
        <a class="btn btn-danger pull-right disable">Désactiver</a>
    </div>

    <div class="loading-box" id="loading-box-aois">
        <p>
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br/>
        </p>
    </div>

    <div class="panel-body" id="panel-box-aois">
        <div class="col-md-2 trials">
            <select id="aoisF" name="aois[]" multiple="multiple">                                          
            </select>
        </div> 

        <div id="aoisDetails" class="col-md-10">
            <table class="table table-striped table-filters">
                    <tr>
                        <th>Moyennes</th>
                        <th colspan="4">Filtres</th>
                    </tr>
                    
                    <tr>
                        <td class="avr-column"><span id="avr_tff">0</span></td>
                        <td>Durée avant 1ère fixation (TFF)</td>

                        <td><input type="text" class="range-input" id="range-input-tff"></td>
                        <td>
                            <input class="slider" id="tff" data-slider-id='tffSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                                <input type="checkbox" id="lesstff" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')">                    
                                <span id="min_tff" >0 </span> <strong> à </strong>  
                                <span class="range_tff" style="color: #269abc">0</span>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moretff" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_tff" style="color: #269abc">0 </span> <strong > à </strong>
                                <span id="max_tff">0 </span>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="avr-column"><span id="avr_tfc">0</span></td>
                        <td>Durée avant 1er clic (TFC)</td>

                        <td><input type="text" class="range-input" id="range-input-tfc"></td>
                        <td>
                            <input class="slider" id="tfc" data-slider-id='tfcSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                            <input type="checkbox" id="lesstfc" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')">                
                                <span id="min_tfc" >0 </span> <strong> à </strong> 
                                <span class="range_tfc" style=" color: #269abc">0 </span>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moretfc" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_tfc" style=" color: #269abc">0 </span> <strong > à </strong>
                                <span id="max_tfc">0 </span>
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <td class="avr-column"><span id="avr_tff-tfc">0</span></td>
                        <td>Durée entre 1ère fixation et 1er clic</td>

                        <td><input type="text" class="range-input" id="range-input-tff-tfc"></td>
                        <td>
                            <input class="slider" id="tff-tfc"  data-slider-id='tff-tfcSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                                <input type="checkbox" id="lesstff-tfc" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')"> 
                                <span id="min_tff-tfc" >0 </span> <strong> à </strong> 
                                <span class="range_tff-tfc" style="color: #269abc">0 </span>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moretff-tfc" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_tff-tfc" style="color: #269abc">0 </span> <strong > à </strong>
                                <span id="max_tff-tfc">0 </span>
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <td class="avr-column"><span id="avr_time_f">0</span></td>
                        <td>Durée totale de fixations</td>

                        <td><input type="text" class="range-input" id="range-input-time_f"></td>
                        <td>
                            <input class="slider" id="time_f"  data-slider-id='time_fSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                                <input type="checkbox" id="lesstime_f" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')"> 
                                <span id="min_time_f" style="margin-left: 3%;">0 </span> <strong> à </strong> 
                                <span class="range_time_f" style="color: #269abc">0 </span> 
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moretime_f" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_time_f" style="color: #269abc">0 </span> <strong > à </strong>
                                <span id="max_time_f">0 </span> 
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <td class="avr-column"><span id="avr_time_relative">0</span></td>
                        <td>Durée relative de fixations par p/r page</td>

                        <td><input type="text" class="range-input" id="range-input-time_relative"></td>
                        <td>
                            <input class="slider" id="time_relative"  data-slider-id='time_relativeSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="0.1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                                <input type="checkbox" id="lesstime_relative" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')"> 
                                <span id="min_time_relative" >0 </span> <strong> à </strong> 
                                <span class="range_time_relative" style="color: #269abc">0 </span>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moretime_relative" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_time_relative" style="color: #269abc">0 </span>
                                <strong> à </strong> 
                                <span id="max_time_relative">0 </span>  
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="avr-column"><span id="avr_aoi_noc">0</span></td>
                        <td>Nombre de clic</td>

                        <td><input type="text" class="range-input" id="range-input-aoi_noc"></td>
                        <td>
                            <input class="slider" id="aoi_noc" data-slider-id='aoi_nocSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)" />
                        </td>
                        
                        <td>
                            <div>
                                <input type="checkbox" id="lessaoi_noc" style="margin-left: 3%;" onchange="addToFilters(this.id, 'aoiFilters')">                 
                                <span id="min_aoi_noc" >0 </span> <strong> à </strong> 
                                <span class="range_aoi_noc" style="color: #269abc">0 </span>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="moreaoi_noc" style="margin-left: 3%" onchange="addToFilters(this.id, 'aoiFilters')">
                                <span class="range_aoi_noc" style="color: #269abc">0 </span> <strong> à </strong> 
                                <span id="max_aoi_noc">0 </span>   
                            </div>
                        </td>
                    </tr>
            </table>


            <input type="checkbox" id="aoi_novisited" onchange="changeFilterMode(this.id, 'aoiFilters')"> N'a pas visité


        </div>
    </div>
</div>