<script setup lang="ts">
import {RouterLink} from "vue-router";

</script>

<template>
  <div id="footerMainContainer">

    <div id="footerUpperContainer">

      <div>

        <img src="../assets/icons/logoIcon.svg" id="logoNavBar" alt="Logo of OnlyDance">

      </div>

      <div id="visionContainerFooter">

        <span id="visionTextFooter">"Know what to dance!"</span>
        <span>- OnlyDance 2025</span>

      </div>

    </div>

    <nav id="footerLinksContainer">

      <div id="innerFooterLinksContainer">

        <RouterLink to="/signup" class="linksFooter">SignUp</RouterLink>
        <span>|</span>
        <RouterLink to="/login" class="linksFooter">Login</RouterLink>
        <span>|</span>
        <RouterLink to="/mainpage" class="linksFooter">MainPage</RouterLink>
        <span>|</span>
        <RouterLink to="/checklists" class="linksFooter">Checklisten</RouterLink>
        <span>|</span>
        <RouterLink to="/imprint" class="linksFooter">Impressum</RouterLink>

      </div>

    </nav>

  </div>
</template>

<style scoped lang="scss">



#footerMainContainer {
  background-color: $colorVioletLight;
  color: $colorWhite;

  #footerUpperContainer {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    height: 35vh;

    img {
      width: 200px;
      height: 200px;
    }

    #visionContainerFooter {
      display: flex;
      flex-direction: column;

      #visionTextFooter {
        font-size: 35px;
      }
    }
  }

  #footerLinksContainer {
    padding-bottom: 20px;
    width: 100%;
    display: flex;
    justify-content: center;

    #innerFooterLinksContainer {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-around;
      width: 80%;

      .linksFooter {
        text-decoration: none;
        color: $colorWhite;
      }

      .linksFooter:hover {
        text-decoration-line: underline;
      }
    }
  }
}

@media (max-width: 600px) {
  #footerMainContainer {
    #footerUpperContainer {
      flex-direction: column;
      text-align: center;
      gap: 20px;
      height: auto;
      padding: 30px 0;

      img {
        width: 120px;
        height: 120px;
      }
    }

    #footerLinksContainer {
      #innerFooterLinksContainer {
        flex-direction: column;
        gap: 15px;

        span {
          display: none; // Hide the separator pipes in mobile view
        }
      }
    }
  }
}
</style>