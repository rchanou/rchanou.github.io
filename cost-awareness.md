## Intro

Now that I'm over a decade into my programming career, I can confidently say that most of the programming advice I've received over the years is bad, and that the general culture of software development has been steering us in the wrong direction. Like many other developers, I went through a mild episode of [this meme](https://www.reddit.com/r/ProgrammerHumor/comments/x5sle0/something_i_have_noticed_as_juniors_become/):

![Bell curve meme showing junior programmer saying "I will write only the code needed to solve the problem", intermediate programmer crying about how you should use SOLID, design patterns, MVC, etc. and senior programmer saying "I will write only the code needed to solve the problem".](/assets/programmer_bell_curve_meme.webp)

It's not that I fell in love with SOLID, Clean Architecture, Domain Driven Design or design patterns and started using them everywhere. In fact, it's kind of the opposite: I felt that I _should_ be using them but never really quite _got_ them. I felt inadequate.

Then, I jumped on the "Functional Programming" train in 2014, when people were noticing React was "good" because it was way simpler than Angular, the reigning state-of-the-art framework at the time. Yeah, it took a while for everyone to realize Angular sucked. That's when I started realizing the Emperor wasn't wearing any clothes. I was still a relative noob, but you 10x FAANG gurus don't _really_ know what you're doing, either.

Initially, React started as a simple "Whoa, dude, the view is just a function of state". Basically, they stole the idea of immediate-mode interfaces, then caked that over the DOM, which is basically the equivalent of pouring concrete over a toxic landfill. Almost _immediately_ after that, they started going down the same path as Angular.

Any of y'all remember the Flux architecture proposed by Facebook? Then a dozen variations popped up with some corny _Back to the Future_-themed name like Marty. Then Redux somehow became the standard. Then everyone realized it sucked to write 10 extra lines to pretend you don't have state. It's the programmer equivalent of a behaviorist saying "my heart rate is elevated and my lips are curled upward" instead of "I'm happy".

So they switched to Vue or Svelte or whatever. (I used Mobx without the class crap, myself.) Anyone remember Functional Reactive Programming, too? Netflix was hyping RxJS pretty hard, made it sound like a good idea, then it turned out to suck. Their engineering team was way ahead of their production studio when it came to releasing high budget slop.

Finally, a few years ago I started to find talks and articles by engineers who were able to scratch that nagging itch in the back of my head: Why does this not feel quite right?

Here are some choice videos on YouTube (if you're too busy to watch these, I recommend at least listening to them while you're working or doing chores):

- The 3-part series _[Object-Oriented Programming is Bad](https://youtu.be/QM1iUe6IofM?si=GQHNLsGfGn0sbEGk)/[Embarrassing](https://youtu.be/IRTfhkiAqPw?si=M4uR-1Kz6Ga0opdY)/[Garbage](https://youtu.be/V6VP-2aIcSc?si=F_XTuR17209RYd8t)_ by Brian Will
- _["Clean Code" is bad. What makes code "maintainable"?](https://youtu.be/V6VP-2aIcSc?si=F_XTuR17209RYd8t)_ by Internet of Bugs
- [Shawn Mcgrath's legendary OOP Rant](https://youtu.be/C90H3ZueZMM?si=_TFHYmo-30P8xSBG)
- _[Solving the Right Problems for Engine Programmers](https://youtu.be/4B00hV3wmMY?si=Hk_v2Hola2ehbpnA)_ by Mike Acton (despite the title, the advice applies to other domains)
- _[Where Does Bad Code Come From?](https://youtu.be/7YpFGkG-u1w?si=Fd9wMoVtAr1j-Fz7)_ by Casey Muratori

Well, I thought about the many techniques that I employ, and they all seem to be some variation of these core tenets:

- **Cost Awareness**
- **Human Orientation**
- **Ahead-of-time** evaluation and assertions
- **Reproducibility**
- **Minimize** variability, ambiguity, duplication, and dead-ends

I guess this is my response to the SOLID principles: use **The **CHARM** Method** instead. Cute, no?

Anyway, this article will focus on Cost Awareness, since that is usually the first principle I reach for when starting work on a new app or feature. It's much like how an artist might sketch a broad outline before filling in all the details. I generally organize my functions along lines of specific, narrowly-defined costs, rather than vague notions of "domains", "responsibilities" or "services".

## What is Cost Awareness?

The easiest way for me to explain this is by example. So what we'll do is review a list of Go function headers and, based only on their names and type definitions, we are going to guess what other properties they have. (For some of these, I just took Go standard library functions and gave them more intuitive names.)

```
func Sum(addends...int) int
func Ceil(x float64) float64 // same as math.Ceil
func ToUpperCase(str string) string // same as strings.ToUpper
func PrintLine(a ...any) (int,error) // same as fmt.Println
func GetRandomInt(max int) int // same as rand.Intn
func GenerateWorldFromSeed(seed int) *World
func ConvertStringToBytes(str string) []byte
func GetSHA256Hash(str string) string
func Sleep(d Duration)
func ConvertStringToInt(string) (int, error) // same as strconv.Atoi
func ScanUserInputLine(a ...any) (int, error) // same as fmt.Scanln
func GetCurrentTimeNow() Time // same as time.Now
func GetStartOfNextHourFromNow() Time
func GetStartOfNextHour(time *Time) Time
func GenerateHashFromPassword(password []byte) ([]byte, error)
func ReadFile(name string) ([]byte, error)
func WriteFile(name string, data []byte, perm FileMode) error
func WriteTempFile(name string) error
func WriteToRotatingLog(logDir, str string) error
func SendEmail(email *Email) error
func PrepareCustomerThankYouEmail(cust *Customer, body string) *Email
func CreateDBRec(a any) error
func DeleteDBRec(a any) error
func UpdateDBRec(a any) error
func SelectQuery(a any) error
func PrepareComplexQuery(a any) (*Query, error)
func QueryOpenOrdersThenDownloadRelatedFilesThenMergeToPDFThenUpload() error
func HTTPGet(url string) (resp *Response, err error)
func HTTPPost(req *Request) (resp *Response, err error)
func FetchListOfYouTubeVideos(req *Request) (resp *Response, err error)
func ChargeCreditCard (cc *CreditCard, amount float64) error
func SendDocumentToPrinter(printer *Printer, doc *Document) error
func LaunchRocket(plan \*LaunchPlan) error
func DestroyCity(name string) (int, error)
```

### Ready? Here are my answers…

For each function, I wrote a code comment with my own "cost-based" annotations, plus an explanation.

```
func Sum(addends...int) int
// pure
```

Let's start simple. `Sum` has the properties of a pure function: for a given set of input parameters, it always returns the same output, and it has no side effects. It requires a CPU and RAM to run, but that's implied for all functions; we know those requirements are met if we can run this program in the first place.

```
func Ceil(x float64) float64
// pure
```

This is also a pure function, just with different input/output types.

```
func ToUpperCase(str string) string
// pure
```

Same here, assuming this function only serves to uppercase strings.

```
func PrintLine(a ...any)
// effect; contained
```

Now we have our first side effect: printing to the terminal. It's very low-cost; so much so that these can remain littered in code long after they are needed, without anyone noticing. Much like garbage collection for memory, you can reasonably assume that the app has a built-in means of "cleaning up" terminal lines; that is, removing the oldest ones. Thus, its costs are contained. You could _almost_ treat this like any pure function.

```
func GetRandomInt(max int) int
// nondeterministic
```

Now we have a function that can return different outputs for multiple calls with the same input. Although running this might have a low computational cost, the nondeterminism adds a "meta-cost" that affects any _human_ working with the code. It does so by decreasing the predictability of the output, not just for this function, but for any subsequent functions.

```
func GenerateWorldFromSeed(seed int) *World
// pure
```

If you're into games that employ procedural generation (such as many roguelikes) you're probably familiar with the concept of a seed: a single value that serves as an input to the game's generation algorithm, returning the same game world for that value every time. Besides making the generation logic easier to debug for its developers, this allows players to share seeds and play the same "runs", some of which might be particularly desirable or intriguing. Even though the generated world returned by this algorithm can be quite complex, it is still a pure function, mu>ch like the simple Sum.

This will be a recurring theme: pushing non-deterministic data and events out to the "edges" of the system, and keeping the "core" deterministic. (Note that I didn't say "impure" and "pure" like a functional programmer; I'll elaborate on that as we continue.)

Another way to see it: non-deterministic functions have a specific qualitative cost–they lose predictability and reproducibility. Any scientist knows that a valid experiment must be reproducible. Developers should similarly value reproducibility: highly reproducible code is highly testable and verifiable code.

```
func ConvertStringToBytes(str string) /*obfuscated*/ []byte
// pure
```

Much like the first few functions we reviewed, for a given input, this always returns the same output. However, that output has a different quality compared to what we saw before: it is not easily readable to most humans. If for some reason you wanted to unit-test this function, you likely wouldn't be hand-writing the outputs you wish to assert. We know in our heads that the result of ToUpper("apple") should be "Apple", but how many of us could say the same for ConvertStringToBytes("apple")?

```
func GetSHA256Hash(str string) /*obfuscated*/ string
// pure
```

Like the previous function, for a given input you wouldn't quickly "know" or be able to handwrite the output. To put it back in cost-aware terms, the "cost" here is human readability. This might not directly affect how you use it in your code, but probably affects how you approach testing it, or any other functions that use it.

```
func Sleep(d Duration)
// delaying
```

This function is technically "pure"; in fact, it returns nothing. But it does use one important resource: time. The time cost of `Sleep` directly correlates with the duration passed into it.

All types of resource consumption essentially convert into two "final" costs: energy usage and time. We might care a bit about the former, but we usually care a lot more about the latter. Big O is a notation that approximates time. We care to distinguish between getting data from the CPU cache, memory, disk and the network, because their access times can differ by several orders of magnitude. Game rendering has a strict time budget in order to achieve a target FPS. So, it's important to note when a function can increase "time consumption", even without computation (and even if that's the desired outcome).

```
func ConvertStringToInt(string) (int, error)
// pure
```

This function attempts to convert the given string into an int. It is a pure function which returns an error for any string inputs that it cannot interpret, such as "twelve" or "12a". It demonstrates how Go functions can return multiple return values, with the last value usually being any error that occurred while running the called function. If no error occurred, the value of error is nil. In this case, even with the additional error return value, the function remains pure, but we'll see how this can change with other kinds of functions…

```
func ScanUserInputLine(a /*mutated*/...any) (int, error)
// nondeterministic; delaying; mutating effect
```

This waits for the user to input a line of text, then assigns that input value to the variable reference(s) passed into it. It returns the number of items successfully scanned, and any error that occurred.

Like GetRandomInt, it has a nondeterministic output. Like Sleep, it causes a delay, blocking further execution of the thread until the user enters their input.

However, unlike the previous functions, it potentially mutates the variables passed into it. For most applications, mutable state is necessary, but is a likely source of bugs. So you might want to make note of what state can be shared among multiple functions, and when that might be mutated.

This is also where consistent naming conventions help. For my own mutating functions, I commonly use a `Set` prefix; so for this function, I might prefer a name like `SetRefsFromUserInput`.

Conversely, you could use a "subword" to distinguish globally shared mutable references, such as "Instance" or "Ref". (Hungarian notation didn't die, we just evolved it to fill in the remaining holes in our type systems.)

```
func GetCurrentTimeNow() Time
// nondeterministic
```

The returned output is different for every time this is called…literally.

```
func GetStartOfNextHourFromNow() Time
// nondeterministic
```

Since this function accepts no parameters, it is implied that the calculation is based on the always-changing current time. It may very well call the nondeterministic function above, GetCurrentTimeNow. In that case, you could theoretically infer that this function itself is nondeterministic, using a sort of static "cost annotation checker" tool. Something to think about.

```
func GetStartOfNextHourFrom(time *Time) Time
// pure
```

By parameterizing the time passed into the start-of-next-hour calculation, we maintain the purity of the calculation. We have again "pushed" the source of nondeterminism to the "edge".

```
func GenerateHashFromPassword(password /*sensitive*/ []byte) ([]byte, error)
// pure
```

This is a pure function, but with one caveat that I think is worth noting: the input value being passed in is likely sensitive data that should not be persisted. This may affect how you choose to perform logging or testing around the function.

```
func ReadFile(name string) ([]byte, error)
// requires: disk/filesys; contained
```

Here is our first function that relies on a consumable dependency besides memory. In this case, that dependency is the disk and filesystem. However, the act of running `ReadFile` doesn't actually consume more of the resource itself; it simply requires it.

You can also think of the disk as an implicit parameter to ReadFile. You can expect the same output for the same input _if_ the relevant disk state also remains the same between subsequent calls. This contrasts with `GetRandomInt` and `GetCurrentTimeNow`, which are expected to return a different, largely unpredictable result every time you call them. Even if the call results in an error because of some file issue (like lack of permissions), we expect it to return the same error for each call with that particular "invalid" disk state.

### Tangent Time: The Fallacy of "Testable Code"

This is where you would commonly be told you should do something like isolate operations that touch the filesystem into a service dependency that you then inject into every other service that uses it. That way you can mock out the filesystem for unit-testing.

I disagree, and to explain, I'll simply defer to the iconoclastic David Heinemeier Hansson. Here are some key quotes I vibe with, from his posts [TDD is Dead](https://dhh.dk/2014/tdd-is-dead-long-live-testing.html) and [Test-Induced Design Damage](https://dhh.dk/2014/test-induced-design-damage.html):

> Test-first units leads to an overly complex web of intermediary objects and indirection in order to avoid doing anything that's "slow". Like hitting the database. Or file IO.

> The fear of letting [tests] talk to the database is outdated. This decoupling is simply not worth it any more, even if it may once have been.

> You do not let your tests drive your design, you let your design drive your tests!

> Stop obsessing about unit tests, embrace backfilling of tests when you're happy with the design, and strive for overall system clarity.

I have my own thoughts here, but that exploration deserves its own article. (Read to the end for a teaser.)

Getting back to `ReadFile`, this specifically

```
func WriteFile(name string, data []byte, perm FileMode) error
// consumes: disk/filesys; uncontained; destructive
```

Like ReadFile, this function requires the disk.

```
func WriteNewTemporaryFile(name string, data []byte, perm FileMode) error
// consumes: disk/filesys; contained; idempotent
```

```
func WriteToRotatingLog(logDir, str string) error
// consumes: disk; cont
```

it a

```
func SendEmail(email *Email) error
// requires: network; irrevocable
```

at

I find it a bit amusing that…

## In summary…

- Organize and label your procedures by costs.
- Keep most of your logic in lower-cost procedures.
- Costs entail not just the physical resources required for a given procedure to run, but qualitative "meta-costs", such as whether the output keeps or loses predictability, readability, etc.
- Ensure you have mechanisms for containing and recouping costs.
- The ultimate costs we should minimize are our human time and energy, both for developers and end-users.

Sounds like common sense, right? Well, based on my experience and observations, I don't think it's obvious to many developers. To illustrate that, note what I didn't say earlier.

I didn't say functions must be shorter than some arbitrary number of lines. I didn't say you need to use getters and setters with private variables and methods to hide implementation details inside class objects. I didn't say you should prefer polymorphism over "if" and "switch" statements, or replace all your imperative for-loops with map/reduce/filter chains. I didn't say you need to use curried higher-order functions, or model all your side effects as monads. And I _definitely_ didn't say concrete implementation details should depend on abstractions.

## "BuT wHaT aBoUt Ai?"

The development of LLMs and other AI tech for code generation does not change my thesis. If anything, it strengthens it. Beyond a small portion of entertainment-oriented novelty apps, we won't be able to get away with not understanding our code. If you disagree, you do you, but mark my words, you're inevitably going to hit a wall…so at the very least, slow your roll, lest you crash and burn at 90 MPH.

No matter how code is written or generated, it should be designed to be understandable by humans. As AI increases the rate at which code proliferates (for better or worse), we will have to increase our ability to understand code, and CHARM will remain a useful tool for that.

## Proposal: A Cost-Aware Development Tool

> We spend so much time as an industry building tools to...refactor our code, or move the text, or collapse the text...but almost no time solving the actual problem that we need to deal with, which is analyzing our data throughout the whole process.
>
> -- <cite>Mike Acton</cite>

There's this concept of an "omniscient debugger" which has been tried a few times in various languages, but never really caught on. Despite that, I see potential in a similar development utility that analyzes simple cost-aware assertions (such as with the functions above) to automatically add instrumentation to code. Natural usage of the instrumented program could automatically generate robust, exhaustive test suites, mock implementations, execution traces, visualizations and more. And no, it wouldn't use AI (although that might actually be quite complementary).
=I know this all sounds overly hand-wavy, but I have a pretty clear vision in my head for how this would work. It's a potential solution to many of the day-to-day problems I personally face; more so than any fancy language feature or design pattern could ever do. I am attempting to develop a proof-of-concept in what little free time I have, and I hope to share what I have soon.

```TRASH TRASH TRASH ~~~~~~~~~~~~~~~~~~~~~~

You should keep your functions small. You should use getters and setters with private variables and methods to hide implementation details inside class objects. Prefer polymorphism over "if" and "switch" statements. Replace all your imperative for-loops with map/reduce/filter chains. Use curried higher-order functions and model all your side effects as monads. Concrete implementation details should depend on abstractions.

Here's what's wrong with these pervasively taught ideas: they _sound_ good in a vacuum, but often don't work out in practice, because they all have cost and drawbacks which usually aren't mentioned (or even noticed) when they are first proposed. I could write several articles discussing the specific pros and cons of each of these ideas, but for now, I'll just say that they tend to increase friction, indirection, and ambiguity, while not solving any problems that _actually_ matter.

In fact, I have a sort of litmus test for these techniques: if some overzealous team lead were to require it as a rule, fullstop, for every line of code, how would that affect the codebase? That may sound like a strawman, but that is literally what happens. It's why `AbstractSingletonProxyFactoryBean` is a real thing. It's why some projects force you to wade through logic fragmented into a thousand different files that each have one class defined in them. I _wish_ crap like this and Onion Architecture were parodies, but alas, they're not.

Now I know a bunch of you are ready to jump in and say, "_Of course_ you shouldn't apply these everywhere, they're just tools, use the right tool for the right job, hammers and screwdrivers", etc.

Well then, why don't we call Object-_Assisted_ Programming instead of Object-_Oriented_ Programming? Or SOLID _Guidelines_ instead of SOLID _Principles_? If SOLID has caveats, shouldn't they add letters to handle and _internalize_ that, helping us determine when we should and shouldn't use them? Proponents teach SOLID in a way that implicitly tells impressionable programmers to use it _everywhere_, even if they expressively deny that.

Try this, look up how the average article or video on these topics is written. In fact, make it a drinking game:

- Take a shot every time they use a "Bad Way" vs. "Good Way" comparison example for each principle. Take two shots if they use cringier terms like "Noob" vs. "Expert".
- Take a shot in memory pure function
- Take a shot if they use some example that's lazily modeled or analogized on something in the real world, like showing you how to make a "HamburgerProvider" that takes a "CookingStrategy" or some crap like that.
- Now, if they _do_ add the caveat that you shouldn't apply these principles everywhere, take a shot if _they leave it at that_. It's such an unhelpful copout, a tautology to shield them from any criticism: "these ideas are good until they're not".

So when _are_ these ideas actually good? Well, I'd say it's when they happen to align with "The CHARM Method" I described earlier. (Ugh, I already hate that acronym, but whatever.) Ironically, CHARM provides clearer answers for the "when" and "why" of SOLID, compared to what SOLID's own acolytes might suggest.

What I like about CHARM is that it scales in all directions, up and down in size, forward and backward in time. The tenets already holistically "account" for each other. You know, like _actual_ principles.

business app vs game vs hdd/memory vs bureaucracy (real reason)

I'm not claiming that CHARM makes me some 10x rockstar that can style on these Clean Code plebs. But what I can say is that I've worked on and taken over systems that were clearly negatively impacted by this prevailing culture of over-abstraction. By shifting development to a more grounded approach, I have been able to significantly improve them in several aspects such as the reduction of bugs, ability to add new features that work reliably in a timely manner, general user satisfaction, and developer sanity.
```
