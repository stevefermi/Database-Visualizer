function getTables(uid,dbid){
    var userid = uid;
    var  databaseid = dbid;
    var data = {"did":databaseid,"userid":userid};
    getAjax( "POST",'http://159.203.251.186/dbhost/querytables',data,function(msg){
        if(msg.code ==1){
            var result = msg.result;
            var html = '<option value="">数据表选择</option>';
    
             for(var tmp in result)
            {

                html+='<option value="';
                html+=result[tmp]['TABLE_NAME'];
                html+='">';
                html+=result[tmp]['TABLE_NAME'];
                html+="</option>";
            
            }
            $('#showsheetlist').html(html);
        }
        
    });  
}

function getColumn(uid,dbid,tname){
    var userid = uid;
    var databaseid = dbid;
    var tablename =  tname;
    var data = {"did":databaseid,"userid":userid,"tablename":tablename};
    getAjax( "POST",'http://159.203.251.186/dbhost/querycolumn',data,function(msg){
        console.log(msg);
        if(msg.code ==1){
            var result = msg.result;
            var xhtml = '<option value="">X轴坐标</option>';
            var yhtml = '<option value="">Y轴坐标</option>'; 
             for(var tmp in result)
            {

                xhtml+='<option value="';
                xhtml+=result[tmp]['COLUMN_NAME'];
                xhtml+='">';
                xhtml+=result[tmp]['COLUMN_NAME'];
                xhtml+="</option>";

                yhtml+='<option value="';
                yhtml+=result[tmp]['COLUMN_NAME'];
                yhtml+='">';
                yhtml+=result[tmp]['COLUMN_NAME'];
                yhtml+="</option>";
            
            }
            $('#x').html(xhtml);
            $('#y').html(yhtml);
            console.log("done");  
        }
        
    });  

}
var tablename = "";
function run(tname){
    tablename = tname;
     if(tablename != ""){
        getColumn(localStorage.getItem("uid"),dbid,tablename);
      }
      else{
        alert("请选择数据表！");
      }
}
function draw(uid,dbid,tablename,type,x,y){
    if(tablename == '' || type == '' || x == "" || y == ""){
        alert("请选择完全！");
    }
    else{
        
        console.log("uid:"+uid+";dbid:"+dbid+";tablename:"+tablename+";x:"+x+";y:"+y+";type:"+type);
        var userid = uid;
        var databaseid = dbid;
        var tname =  tablename;
        var x_axis = x;
        var y_axis = y;
        var type = type;
        chart.clear();
        var data = {"did":databaseid,"userid":userid,"tablename":tname};
        getAjax( "POST",'http://159.203.251.186/dbhost/querydata',data,function(msg){
            if (msg.code ==1) {
                var result = msg.result;
                console.log(result);
                var graphData = [];
                $.each(result,function(index,data) {
                    graphData.push(JSON.parse('{"'+x_axis+'":"'+data[x_axis]+'","'+y_axis+'":"'+data[y_axis]+'"}'));
           
                })              
                console.log("graphData:"+graphData);
                chart.source(graphData);
                if(type == "0"){
                    chart.point().position(x_axis+'*'+y_axis).color(x_axis);
                }
                if (type == "1") {
                    chart.line().position(x+'*'+y).color(x);
                }
                if (type == "2") {
                    var Stat = G2.Stat;
                    chart.interval().position(Stat.summary.mean(x_axis+'*'+y_axis)).color(x_axis);
                }
                chart.render();
                console.log("done");
            }
       
        }); 
    } 

}

