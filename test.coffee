class Thing

class Person extends Thing
  initialize: (name, age) ->
    @name = name
    @age = age

  @speak: (words) ->
    console.log words


people =
  name: 'andrew'
  pets:
    cat: 'tom'
    dog: 'bob'