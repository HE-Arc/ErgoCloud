<div class="panel panel-primary" id="filterTest" >
    <div class="panel-heading panel-heading-controls">
        <h1>Filtres sur pages</h1>
        <a class="btn btn-danger pull-right disable">Désactiver</a>
    </div>

     <div class="loading-box" id="loading-box-trials">
        <p>
            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br/>
        </p>
    </div>

    <div class="panel-body" id="panel-box-trials">
        <div class="col-md-2 trials">           
            <select id="trialsF" name="trials[]" multiple="multiple"  >                                          
            </select>
        </div>

        <div id="trialsDetails" class="col-md-10">  


            <table class="table table-striped table-filters">
                <tr>
                    <th>Moyennes</th>
                    <th colspan="4">Filtres</th>
                </tr>
                
                <tr>
                    <td class="avr-column"><span id="avr_duration">0</span></td>
                    <td>Durée sur la page</td>

                    <td><input type="text" class="range-input" id="range-input-trialduration"></td>
                    <td>
                        <input class="slider" id="trialduration"  data-slider-id='durationTrialSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)" />
                    </td>
                    
                    <td>
                        <div>
                            <input type="checkbox" id="lesstrialduration" style="margin-left: 3%;" onchange="addToFilters(this.id, 'trialFilters')"> 
                            <span id="min_trialduration">0 </span> <strong> à </strong>  
                            <span class="range_trialduration" style="color: #269abc">0 </span>
                        </div>
                        
                        <div>
                            <input type="checkbox" id="moretrialduration" style="margin-left: 3%" onchange="addToFilters(this.id, 'trialFilters')">  
                            <span class="range_trialduration" style="color: #269abc">0 </span> <strong > à </strong>    
                            <span id="max_trialduration">0 </span> 
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="avr-column"><span id="avr_noc">0</span></td>
                    <td>Nombre de clics</td>

                    <td><input type="text" class="range-input" id="range-input-noc"></td>
                    <td>
                        <input class="slider" id="noc" data-slider-id='nocSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)"/>
                    </td>
                    
                    <td>
                        <div>
                            <input type="checkbox" id="lessnoc" style="margin-left: 3%;" onchange="addToFilters(this.id, 'trialFilters')">
                            <span id="min_noc" >0 </span> <strong >à</strong>    
                            <span class="range_noc" style="color: #269abc">0 </span>                
                        </div>
                        
                        <div>
                            <input type="checkbox" id="morenoc" style="margin-left: 3%" onchange="addToFilters(this.id, 'trialFilters')">
                            <span class="range_noc" style="color: #269abc">0 </span>
                            <strong>à</strong> 
                            <span id="max_noc">0</span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="avr-column"><span id="avr_nos">0</span></td>
                    <td>Nombre de scrolls</td>

                    <td><input type="text" class="range-input" id="range-input-nos"></td>
                    <td>
                        <input class="slider" id="nos" data-slider-id='nosSlider' type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" onchange="showValue(this.value, this.id)"/>
                    </td>
                    
                    <td>
                        <div>
                            <input type="checkbox" id="lessnos" style="margin-left: 3%;" onchange="addToFilters(this.id, 'trialFilters')">
                            <span id="min_nos">0 </span> <strong >à</strong>                 
                            <span class="range_nos" style="color: #269abc">0 </span>    
                        </div>
                        
                        <div>
                            <input type="checkbox" id="morenos" style="margin-left: 3%" onchange="addToFilters(this.id, 'trialFilters')">
                            <span class="range_nos" style="color: #269abc">0 </span> <strong>à</strong> 
                            <span id="max_nos">0</span>
                        </div>
                    </td>
                </tr>
            </table>

            <input type="checkbox" id="trial_novisited" onchange="changeFilterMode(this.id, 'trialFilters')"> N'a pas visité

        </div>  
    </div>
</div>