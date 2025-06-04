<template>
  <div class="impressum-page">
    <section class="form-section">
      <h1 class="title">Impressum</h1>
      <div class="impressum-content">


        <p><strong>Verantwortlich für den Inhalt der Seite:</strong></p>
        <p>
          Andreas Juchelka, Erik Weidenauer, Theodor Widmer

        </p>

        <p><strong>Haftungsausschluss:</strong><br />
           Der Inhalt dieser Seite wird nicht moderiert, für den Inhalt sind allein die Nutzer der Seite und nicht die Betreiber verantwortlich.Wir übernehmen keine Haftung für die Inhalte externer Links. Für den Inhalt der verlinkten Seiten sind ausschließlich deren Betreiber verantwortlich.
        </p>
      </div>
    </section>
  </div>
</template>

<style scoped lang="scss">
.impressum-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(to bottom right, #f3e8ff, #fce7f3, #ffffff);
}

.form-section {
  width: 100%;
  max-width: 500px;
  background-color: $colorWhite;
  border-radius: 1.5rem;
  padding: 2rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.title {
  font-size: 2rem;
  color: $colorPurpleLight;
  margin-bottom: 1.5rem;
  text-align: center;
}

.impressum-content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  font-size: 1rem;
  line-height: 1.6;
  color: #333;

  p {
    margin: 0;
  }

  strong {
    color: $colorVioletLight;
  }
}
</style>
