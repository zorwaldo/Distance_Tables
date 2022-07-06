<?php require "php/connect.php";
$i=0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/main.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблицы</title>
</head>
<body>
    <!-- Подключение API гугл карт -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&libraries=geometry"></script>
    <script>
        //Переменные
        let roww; //Переменная отсылающая на таблицу
        let distance; //Переменная в которую записывается дитсанция
        let point1; //Переменная для записи извлеченных координат из базы данных. 
        //У нас в базе данных координаты жк записаны как POINT(1 2), массив point1 будет состоять из двух чисел [1, 2]
    </script>

    <!-- Достаём данные о жилых комплексах -->
    <?php 	$result = mysqli_query($mysql, "SELECT DISTINCT j1.* FROM `jk` as j1"); 
			while($info = mysqli_fetch_assoc($result) and $i<1) {
                //Переменная i - ограничитель. Служит для того, чтобы у нас на странице анализировался один ЖК
                $i++;
                //Если убрать переменную страница будет очень долго грузиться. Но будет выдана информация о всех ЖК
			?> 
            <script>
                //Создаем массив внутрь которого поместим информацию о станциях и расстояниях, после чего - отсортируем массив.
                let arr<?php echo $info["ID_JK"]?> = [];
            </script>
                    <table id="jk<?php echo $info["ID_JK"]?>" style="margin-top: 50px;" width="715"  border="1" align="center" cellspacing="0" cellpadding="10">
                        <tr>
                            <!-- Выводится название жилого комплекса -->
                            <td colspan="2" >ЖК: <b><?php echo $info["Name"]?></td></b>
                        </tr>
                        <tr>
                            <td  width="190"><b> Остановка</b></td>
                            <td> <b>Расстояние</b> </td>
                        </tr>
                        <!-- Далее будем доставать остановки и считать расстояние от жк до каждой остановки -->
                        <?php 	$result2 = mysqli_query($mysql, "SELECT DISTINCT j1.* FROM `ostanovki` as j1"); 
			            while($info2 = mysqli_fetch_assoc($result2) ) {
			            ?> 
                        
                        
                        <script>
                            point1 = "<?php echo $info["Point"]?>"; 
                            point1 = point1.substring(point1.indexOf("(") + 1,point1.indexOf(")"));
                            point1 = point1.split(" ");
                            //Теперь point это массив с двумя числами
                            //Далее идёт подсчет дистанции. Координаты остановок достаём через php.
                            var p1 = new google.maps.LatLng(point1[0], point1[1]);
                            var p2 = new google.maps.LatLng(<?php echo $info2["Longitude_WGS84"]?>, <?php echo $info2["Latitude_WGS84"]?>);
                            distance = calcDistance(p1, p2);

                            //вычисление дистанции между двумя точками
                            function calcDistance(p1, p2) {
                            return (google.maps.geometry.spherical.computeDistanceBetween(p1, p2) / 1000).toFixed(2);
                            }
                            //Если дистанция меньше одного километра, то заносим информацию об остановке в массив.
                            roww = document.querySelector("#jk<?php echo $info["ID_JK"]?>")
                            if(distance<1){
                                var ost<?php echo $info2["ID_ost"]?> = {
                                    name: "<?php echo $info2["Name"]?>",
                                    dist: distance
                                }
                                arr<?php echo $info["ID_JK"]?>.push(ost<?php echo $info2["ID_ost"]?>)
                                //Получаем массив остановок с расстоянием до жк меньше 1 км.
                            }
                        </script>
                        <?php  }?>
                        <script>
                            //Ниже идёт сортировка массива
                            arr<?php echo $info["ID_JK"]?>.sort((a, b) => a.dist > b.dist ? 1 : -1);
                            //выводим массив в таблицу
                            arr<?php echo $info["ID_JK"]?>.forEach(el => {
                                roww.innerHTML+=`
                                <tr">
                                <td  width="190">${el.name}</td>
                                <td>${el.dist*1000} м</td>
                                </tr>
                             `
                            })
                        </script>
                    </table>
			<?php } ?>
</body>
</html>