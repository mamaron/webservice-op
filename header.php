<header class="header">
    <div class="container">
      <h1><a href="index.html">いぬの駅</a></h1>
      <nav>
        <ul>
          <?php if(!empty($_SESSION['login_date'])){ ?>
            <li><a href="logout.php">ログアウト</a></li>
            <li><a href="mypage.php">マイページ</a></li>
          <?php }else{?>
            <li><a href="signup.php">ユーザー登録</a></li>
            <li><a href="login.php">ログイン</a></li>
          <?php }?>
        </ul>
      </nav>
    </div>
  </header>