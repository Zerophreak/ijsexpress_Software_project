using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Text;
using System.Text.Json;
using System.Net;
using System.IO;
using System.Net.Sockets;
using System.IO.Ports;
using System.Threading;
using Gtk;
using System.Net.Http;
using System.Net.Http.Json;

using GLib;
using Object = System.Object;



namespace ijsexpressRFID_Reader
{


    public class Product
    {
        //public string name { get; set; }
        //public string ean { get; set; }
        public string chip_id { get; set; }
        public int freezer_id { get; set; }
        //public string logo_url{ get; set;}
        
    }



    class Program
        {
            public static SerialPort iSerialPort = new();
            private static Timer aTimer;
            private static Action<List<byte>> _processMeasurement;
            private const int SizeOfMeasurement = 4;
            static List<byte> Data = new List<byte>();
            private static List<string> RfidTags = new ();
            private static List<string> RfidTagsTwo = new ();

             private static string PostStatus ;
             
             
            
            //--------------------------------------------------

            static int Main()
            {
                // string[] getPorts = SerialPort.GetPortNames();
                // string? strComPort;
                // if (getPorts.Length ==0 || getPorts== null)
                // {
                //     Console.WriteLine("Geen Seriele ports gevonden");
                //     Console.WriteLine("Herstart het programma");
                //     
                // }
                // else
                // {
                //     Console.WriteLine("De volgende poorten zijn gevonden:");
                //     foreach (string port in getPorts)
                //     {
                //         
                //         Console.WriteLine(port);
                //     
                //     }    
                //     Console.WriteLine("Bij het selecteren van een port type hem correct in:");
                // }
                // strComPort = Console.ReadLine();    
               
                string strComPort = "COM4";
                string strException;
                int Baudrate = 9600;
                
                
                int nRet = OpenCom(strComPort, Baudrate, out strException);
                if (nRet != 0)
                {
                    string strLog = "Connect reader failed, due to: " + strException;
                    Console.WriteLine(strLog);
                    //return;
                }
                else
                {
                    string strLog = $"Reader connected {strComPort}@{Baudrate}";
                    Console.WriteLine(strLog);
                }

                Console.WriteLine("Press any key to exit.");
                Console.ReadKey();

                iSerialPort.Close();
                return 0;
            }

            //--------------------------------------------------

            
            public static int OpenCom(string strPort, int Baudrate, out string strException)
            {
                strException = string.Empty;

                if (iSerialPort.IsOpen)
                {
                    iSerialPort.Close();
                }

                try
                {
                    iSerialPort.PortName = strPort;
                    iSerialPort.BaudRate = Baudrate;
                    iSerialPort.Parity = Parity.None;
                    iSerialPort.StopBits = StopBits.One;
                    iSerialPort.DataBits = 8;
                    iSerialPort.Handshake = Handshake.None;
                    iSerialPort.DataReceived += DataReceivedHandler;
                    iSerialPort.Open();
                }
                catch (Exception ex)
                {
                    strException = ex.Message;
                    return -1;
                }
                return 0;
            }

            

            //--------------------------------------------------


            private static void DataReceivedHandler(object sender, SerialDataReceivedEventArgs e)
            {
                SerialPort sp = (SerialPort)sender;
                string inData = sp.ReadExisting();
                Console.Write(inData);
                List<string> test = new List<string>();
                foreach (string tag in RfidTags)
                {
                    if (tag != inData)
                    {
                        
                     RfidTagsTwo.Add(inData);
                    }
                }

                string serializedTag = ObjectToJson(inData);
               
                 PostStatus = PostHttpRequest(serializedTag);
                    Console.WriteLine(PostStatus);
                iSerialPort.DataReceived -= null;
            }
            
            
            //--------------------------------------------------

           
            static void AddTagsTwo( string indata)
            {
                foreach (string tag in RfidTagsTwo)
                {
                    if (tag != indata)
                    {
                        RfidTagsTwo.Add(indata);
                        
                    }
                    
                    
                    
                } 
            }

            private static List<string> NotInFreezer(List<string> ScannedTags)
            {
                List<string> removed = ScannedTags.Except(RfidTags).ToList();
                return removed;
            }
            
            
            static List<string> RemoveDuplicates(List<string> list)
            {
                List<string> distinct = list.Distinct().ToList();
                return distinct;
            }

            
            //--------------------------------------------------

           
            
            //--------------------------------------------------

            private static string ObjectToJson(string epc)
            {

                Product product = new Product();
                product.freezer_id = 1;
               // product.name = "BJ";
                product.chip_id = epc;
              //  product.ean = "imadeit";
                // product.logo_url =
                var opt = new JsonSerializerOptions() { WriteIndented = true };
                string strJson = JsonSerializer.Serialize<Product>(product, opt);
                
                return strJson;

            }
            //--------------------------------------------------


            private static string PostHttpRequest(string epc)
            {
                var url = "https://eoe6q30oyj1gi9g.m.pipedream.net";
                var urlTwo = "http://ptsv2.com/t/i752l-1655812938/post";
                HttpWebRequest httpWebRequest = (HttpWebRequest)WebRequest.Create(url);
                httpWebRequest.ContentType = "application/json; charset=utf-8";
                httpWebRequest.Method = "POST";
                string result = null;
                using (var streamWriter = new StreamWriter(httpWebRequest.GetRequestStream()))
                {
                    string json = epc;
                    
                    Debug.Write(json);
                    streamWriter.Write(json);
                    streamWriter.Flush();
                    streamWriter.Close();
                }

                try
                {
                    using (var response = httpWebRequest.GetResponse() as HttpWebResponse)
                    {
                        if (httpWebRequest.HaveResponse && response != null)
                        {
                            using (var reader = new StreamReader(response.GetResponseStream()))
                            {
                                result = reader.ReadToEnd();
                            }
                        }
                    }
                }
                catch (WebException e)
                {
                    if (e.Response != null)
                    {
                        using (var errorResponse = (HttpWebResponse)e.Response)
                        {
                            using (var reader = new StreamReader(errorResponse.GetResponseStream()))
                            {
                                string error = reader.ReadToEnd();
                                result = error;
                            }
                        }

                    }
                }
                return result;

            }
          
        }
           // private static void AddBytes(byte[] bytes)
            // {
            //     Data.AddRange(bytes);
            //     while(Data.Count > SizeOfMeasurement)            
            //     {
            //         var measurementData = Data.GetRange(0, SizeOfMeasurement);
            //         Data.RemoveRange(0, SizeOfMeasurement);
            //         if (_processMeasurement != null) _processMeasurement(measurementData);
            //        
            //         Console.Write("measurementdata:"+ measurementData);
            //     }
            //     //ProcessMeasurement(_);
            //
            // }

            // private static void ProcessMeasurement(List<byte> bytes)
            // {
            //     // this will get called for every measurement, so then
            //     // put stuff into a text box.... or do whatever
            // }
            //
            //




            // async static void PostRequest (string epc)
            // { version 2
            //     var url = "https://api.ijsexpress.dev.syurin.com/products?key=qeaIyI6xYzZNPhwk1i3C&";
            //
            //     using HttpClient client = new HttpClient();
            //     var values = new Dictionary<string, string>
            //     {
            //         { "name", "BJ" },
            //         { "ean", "efdfdf" },
            //         { "chip_id", epc },
            //         { "logo_url", "https://www.google.com/imgres?imgurl=https%3A%2F%2Fnerdist.com%2Fwp-content%2Fuploads%2F2020%2F07%2Fmaxresdefault.jpg&imgrefurl=https%3A%2F%2Fnerdist.com%2Farticle%2Fyou-can-now-rick-roll-your-zoom-meetings%2F&tbnid=ywPfYOtLV7CpmM&vet=12ahUKEwiS95DnyKz4AhUwm_0HHUAiCO4QMygBegUIARDJAQ..i&docid=lx6YBTRBMlgIAM&w=1200&h=676&q=rick%20and%20roll&ved=2ahUKEwiS95DnyKz4AhUwm_0HHUAiCO4QMygBegUIARDJAQ" },
            //     
            //         
            //     };
            //     var content = new FormUrlEncodedContent(values);
            //     
            //     
            //     var response =  await client.PostAsync("http://ptsv2.com/t/nnu4m-1655196715/post", content);
            //
            //     var responseString = await response.Content.ReadAsStringAsync();
            //     Console.WriteLine(responseString);

            //Version 1 of POST-HTTPS Request

            // var url = "https://api.ijsexpress.dev.syurin.com/products?key=qeaIyI6xYzZNPhwk1i3C&";
            //
            // var request = WebRequest.Create(url);
            // request.Method = "POST";
            //
            // var product = new Product("B&J", epc, "imadeit", "https://www.google.nl/imgres?imgurl=https%3A%2F%2Fcdn.businessinsider.nl%2Fwp-content%2Fuploads%2F2021%2F02%2F602f006e97cf0.png&imgrefurl=https%3A%2F%2Fwww.businessinsider.nl%2Fyou-can-now-rickroll-your-friends-in-hd-with-a-remastered-version-of-rick-astleys-never-gonna-give-you-up%2F&tbnid=q8dero0SxEu9MM&vet=12ahUKEwiv1PKYsZv4AhUKG-wKHTnfC6AQMygDegUIARDjAQ..i&docid=vAMgqxm9T1zLSM&w=2343&h=1757&q=rick%20roll&hl=en&authuser=0&ved=2ahUKEwiv1PKYsZv4AhUKG-wKHTnfC6AQMygDegUIARDjAQ");
            // var json = JsonSerializer.Serialize(product);
            // byte [] byteArray = Encoding.UTF8.GetBytes(json);
            //
            // request.ContentType = "application/x-www-form-urlencoded";
            // request.ContentLength = byteArray.Length;
            //
            // using var reqStream = request.GetRequestStream();
            // reqStream.Write(byteArray, 0, byteArray.Length);
            //
            // using var response = request.GetResponse();
            // Console.WriteLine(((HttpWebResponse)response).StatusDescription);
            //
            // using var respStream = response.GetResponseStream();
            //
            // using var reader = new StreamReader(respStream);
            // string data = reader.ReadToEnd();
            //var response = await client.PostAsync("http://www.example.com/recepticle.aspx", content);
            //}
            //     
    }

