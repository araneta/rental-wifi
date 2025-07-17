<template>
  <div>
    <nav>
      <router-link to="/">Home</router-link> |
      <router-link to="/login">Login</router-link> |
      <router-link to="/students">Students</router-link>
    </nav>
    <router-view />
  </div>
</template>
