<div class="page-header">
    <h4>他的提问</h4>
</div>
<div class="page-header " ng-repeat="item in Question.all_question[userId]">
    <p><h3><a href="#" ui-sref="question.detail({id:item.id})"> [:item.title:]</a></h3></p>
    <p class="text-muted">[:item.created_at:] · [:item.answers.length:]条回答</p>
</div>

