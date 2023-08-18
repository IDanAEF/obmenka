(function (e) {
  /**
   * Добавление отзыва
  **/
  $("#add_review").submit(function (e) {
      e.preventDefault();
      $.ajax({
          type: "POST",
          url: "/wp-content/themes/obmenka/includes/add-review.php",
          data: $(this).serialize(),
          success: () => {
              console.log("Спасибо. Ваш отзыв отправлен.");
              $(this).trigger("reset"); // очищаем поля формы
          },
          error: () => console.log("Ошибка отправки.");
      });
  });
})(jQuery);
тдтд
