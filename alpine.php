<?php
$user = [
  'name' => 'aaaaaaaaaa',
  'email' => 'aaaaaa@test.com'
];
?>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div 
  x-data='wizardForm(<?= json_encode($user) ?>)'
>
  <form method="POST" action="/guardar.php">

    <section x-show="step === 1">
      <h2>Datos básicos</h2>

      <input 
        type="text" 
        name="name" 
        x-model="form.name"
        placeholder="Nombre"
      >

      <button type="button" @click="step++">
        Siguiente
      </button>
    </section>

    <section x-show="step === 2">
      <h2>Contacto</h2>

      <input 
        type="email" 
        name="email" 
        x-model="form.email"
        placeholder="Email"
      >

      <button type="button" @click="step--">
        Atrás
      </button>

      <button type="submit">
        Guardar
      </button>
    </section>

  </form>
</div>

<script>
function wizardForm(initialData) {
  return {
    step: 1,
    form: {
      name: initialData.name ?? '',
      email: initialData.email ?? ''
    }
  }
}

</script>