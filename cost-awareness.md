## Yup, I'm jumping on the anti-OOP bandwagon.

You are better off literally forgetting about Object-Oriented Programming. Same goes for Functional Programming, SOLID, Clean Code, Hexagonal Architecture, Domain-Driven Design, MVC, and a slew of other buzzwords parroted by countless FAANG-wannabe astronaut architects who've been touched by Uncle Bob. All these ideas fall under the purview of what I call "Conventional Programming Wisdom", which I'll hereafter refer to as **CPW**.

This advice is especially targeted to those young, enthusiastic junior developers, eager to improve themselves by mastering design patterns and so-called "best practices". No, you don't need to study them. That will only set you back in the long run, just like they did for me.

It's not that these schools of thought are totally wrong. They do have some good ideas, and I'm sure they've helped many folks ditch their habit of carelessly copy-pasting spaghetti code. However, the ways they are generally taught are likely to lead developers to overcorrect in the other direction: over-engineered, cargo-culted, excessively abstract code.

I'm hardly the first person to think this; the sentiment seems to be common enough such that this meme achieved mild popularity:

INSERT MEME IMAGE

In fact, you can already find several scathing critiques of CPW, by engineers much more talented than myself. Here are some choice videos on YouTube (if you're too busy to watch these, I recommend at least giving these a listen while you're working or doing chores):

- The 3-part series _Object-Oriented Programming is Bad/Embarrassing/Garbage_ by Brian Will
- _Clean Code is Bad_ by Internet of Bugs
- Shawn Mcgrath's legendary OOP Rant
- _Solving the Right Problems for Engine Programmers_ by Mike Acton (despite the title, this is absolutely applicable outside of engine programming)
- _Where Does Bad Code Come From?_ by Casey Muratori

Note that these videos focus on how CPW harms code maintainability and developer velocity, not performance. Proponents of CPW will often acknowledge the potential performance overhead, but claim it is worth it for the supposedly improved developer experience it brings. Or they'll admit that CPW is worse for small projects or teams, but postulate some magical inflection point where it makes code better as its scope increases.

No, I'm not even conceding that. I am saying that implementing CPW, as it is commonly described and taught, just leads to worse code all around.

So then, what's my alternative proposal for "good code", and why do I think it's better? Well, I thought about the many techniques that I employ, and they seem to boil down to these core tenets:

- **Cost Awareness**
- **Human Orientation**
- **Ahead-of-time** evaluation and assertions
- **Reproducibility**
- **Minimize** variability, ambiguity, duplication, and dead-ends

I guess this is my response to the SOLID acronym: The **CHARM** Method. Cute, no?

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

Let's start simple. Sum has the properties of a pure function: for a given set of input parameters, it always returns the same output, and it has no side effects. It requires a CPU and RAM to run, but that's implied for all functions; we know those requirements are met if we can run this program in the first place.

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

This is also where clear, consistent naming conventions help. For my own mutating functions, a common prefix I use is "Set". For this function, I might prefer a name like SetRefsFromUserInput.

Conversely, you might want a distinct "subword" for globally shared mutable references, such as "Instance" or "Ref". (You thought Hungarian notation was dead? Nah, it just evolved.)

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

Here is our first function that relies on a consumable dependency besides memory. In this case, that dependency is the disk. However, the act of running this function itself doesn't actually consume the resource; it simply requires it.

You can also think of the disk and file system as implicit parameters to ReadFile. You can expect that if the state of the filesystem remains the same between subsequent calls, you will get the same output. This contrasts with GetRandomInt and GetCurrentTimeNow, which are expected to return a different, largely unpredictable result every time you call them. Even if the filesystem is in a "broken" state, we expect it to return the same error for each call with that state.

For ReadFile, you can reasonably test "the happy path" with a modicum of setup.

[TDD is Dead](https://dhh.dk/2014/tdd-is-dead-long-live-testing.html) and [Test-Induced Design Damage](https://dhh.dk/2014/test-induced-design-damage.html) by Mr. Ruby on Rails himself, DHH.

> Test-first units leads to an overly complex web of intermediary objects and indirection in order to avoid doing anything that's "slow". Like hitting the database. Or file IO.

> The fear of letting [tests] talk to the database is outdated. This decoupling is simply not worth it any more, even if it may once have been.

> You do not let your tests drive your design, you let your design drive your tests!

> Stop obsessing about unit tests, embrace backfilling of tests when you're happy with the design, and strive for overall system clarity.

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

Here's what's wrong with these pervasively taught ideas: they _sound_ good in a vacuum, but often don't work out in practice, because they all have cost and drawbacks which usually aren't mentioned (or even noticed) when they are first proposed. I could write several articles discussing the specific pros and cons of each of these ideas, but for now, I'll just say that they tend to increase friction, indirection, and ambiguity, while not solving any problems that _actually_ matter. If anything, their "benefits" largely boil down to superficial aesthetic improvements, and even those are still debatable.

In fact, I have a litmus test for these techniques: if some overzealous team lead were to require it as a rule, fullstop, for every line of code, how would that affect the codebase? That may sound like a strawman, but that is literally what happens. It's why `AbstractSingletonProxyFactoryBean` is a real thing. It's why some projects force you to wade through logic fragmented into a thousand different files that each have one class defined in them. I _wish_ crap like this and Onion Architecture were parodies, but alas, they're not.

Now I know a bunch of you are ready to jump in and say, "_Of course_ you shouldn't apply these everywhere, they're just tools, use the right tool for the right job, hammers and screwdrivers", etc.

Well then, why don't we call Object-_Assisted_ Programming instead of Object-_Oriented_ Programming? Or SOLID _Guidelines_ instead of SOLID _Principles_, ? If there are external caveats, shouldn't SOLID add letters to _internalize_ those, and help us determine when we should and shouldn't use them? Even if not explicitly saying so, the way SOLID is taught is implicitly telling impressionable programmers to use them everywhere.

Try this, look up how the average article or video on these topics is written. In fact, make it a drinking game:

- Take a shot every time they use a "Bad Way" vs. "Good Way" comparison example for each principle. Take two shots if they use cringier terms like "Noob" vs. "Expert".
- Take a shot in memory pure function
- Take a shot if they use some example that's lazily modeled or analogized on something in the real world, like showing you how to make a "HamburgerProvider" that takes a "CookingStrategy" or some crap like that.
- Now, if they _do_ add the caveat that you shouldn't apply these principles everywhere, take a shot if _they leave it at that_. It's such an unhelpful copout, a tautology to shield them from any criticism: "these ideas are good until they're not".

So when _are_ these ideas actually good? Well, I'd say it's when they happen to align with the CHARM method I described earlier. (Ugh, I already hate my own acronym, but it's useful.) Ironically enough, CHARM may actually provide clearer answers for the "when" and "why" of SOLID, compared to what SOLID's own acolytes might suggest.

But I don't even feel the need to think about "should I be using SOLID". To me, CHARM alone offers the best balance between velocity and "getting sh\*t done", while still maintaining the ability to refactor later without fear, and ensure my abstractions are actually helpful by basing them on concrete use cases. That's the opposite of what SOLID recommends, which I think leads to disaster.

What I like about CHARM is that it scales in all directions, up and down in size, forward and backward in time. The tenets already holistically "account" for each other. You know, like _actual_ principles.

business app vs game vs hdd/memory vs bureaucracy (real reason)

I'm not claiming that CHARM makes me some 10x rockstar that can style on these Clean Code plebs. But what I can say is that I've worked on and taken over systems that were clearly negatively impacted by this prevailing culture of over-abstraction. By shifting development to a more grounded approach, I have been able to significantly improve them in several aspects such as the reduction of bugs, ability to add new features that work reliably in a timely manner, general user satisfaction, and developer sanity.

## "BuT wHaT aBoUt Ai?"

The development of LLMs and other AI tech for code generation does not change my thesis. If anything, it strengthens it. Beyond a small portion of entertainment-oriented novelty apps, we won't be able to get away with not understanding our code. If you disagree, you do you, but mark my words, you're inevitably going to hit a wall…so at the very least, slow your roll, lest you crash and burn at 90 MPH.

No matter how code is written or generated, it should be designed to be understandable by humans. As AI increases the rate at which code proliferates (for better or worse), we will have to increase our ability to understand code, and CHARM will remain a useful tool for that.

## Proposal: A Cost-Aware Development Tool

> We spend so much time as an industry building tools to...refactor our code, or move the text, or collapse the text...but almost no time solving the actual problem that we need to deal with, which is analyzing our data throughout the whole process.
>
> -- <cite>Mike Acton</cite>

There's this concept of an "omniscient debugger" which has been tried a few times in various languages, but never really caught on. Despite that, I see potential in a similar development utility that analyzes simple cost-aware assertions (such as with the functions above) to automatically add instrumentation to code. Natural usage of the instrumented program could automatically generate robust, exhaustive test suites, mock implementations, execution traces, visualizations and more. And no, it wouldn't use AI (although that might actually be quite complementary).
=I know this all sounds overly hand-wavy, but I have a pretty clear vision in my head for how this would work. It's a potential solution to many of the day-to-day problems I personally face; more so than any fancy language feature or design pattern could ever do. I am attempting to develop a proof-of-concept in what little free time I have, and I hope to share what I have soon.
