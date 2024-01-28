# Remarques

### hasFund()

La fonction `hasFund()` contient un `!== 0` vérifiant donc que la valeur soit un entier différent de 0 cependant 
la méthode `getBalance()` de la classe `Wallet` retourne un `float` et non un `int` ce qui fait que la condition
ne sera jamais vérifiée.