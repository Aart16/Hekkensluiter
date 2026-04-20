<div class="inside_space colored spaced">gevangenen<a class="a" href="prisoners">view</a></div>
<div class="inside_space colored spaced">cellen<a class="a" href="cells">view</a></div>
<div class="inside_space colored spaced">geschiedenis<a class="a" href="history">view</a></div>
<?php if($this->auth->hasAnyRole(\Delight\Auth\Role::ADMIN)): ?>
    <div class="inside_space colored spaced">accountbeheer<a class="a" href="users">view</a></div>
    <div class="inside_space colored spaced">nieuw account<a class="a" href="register">view</a></div>
<?php endif; ?>