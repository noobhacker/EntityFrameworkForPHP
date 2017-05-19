# EntityFrameworkForPHP
Simple QueryBuilder like ASP.NET Entity Framework.
<p align="center">
  <img src="https://github.com/noobhacker/EntityFrameworkForPHP/blob/master/preview.PNG" width="350"/>
 </p>
 
I created this layer for my own project use and I find it very useful. </br>
This is not a super big many function framework but I build the crucial features for simple LINQ operation.
 
</br>
- 1 core DbSet code file for query builder and 1 BaseContext for mysqli $conn DI.
</br>
- NO $db->saveChanges(), this is only query builder that looks like Entity Framework.

</br>
Do follow the database conventions (I try to follow EF style as much as I can)
</br>
Table name -> plural 
</br>
Foreignkey -> target_table_id
</br>
The join demonstrated above is for one to many only. I will do custom join like the one in C# if my project needs it.
